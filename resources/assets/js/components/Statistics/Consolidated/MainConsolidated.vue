<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div v-if="!state && !is_first" style="padding:30px 45%!important; display:inline-block !important;">
                    <half-circle-spinner
                            :animation-duration="1000"
                            :size="60"
                            :color="'#657fff'"
                    />
                    Cargando...
                </div>
                <template v-if="state">
                    <br>
                    <table-consolidated id="table-consolidated"
                                        v-show="!objectToTableConsolidated.params.filter.isReport && !objectToTableConsolidated.params.filter.isFilterReport"
                                        :objectInput="objectToTableConsolidated"/>
                    <table-report
                            v-show="objectToTableConsolidated.params.filter.isReport"
                            :prop-data="objectToTableConsolidated"/>
                    <table-filter-report
                            v-show="objectToTableConsolidated.params.filter.isFilterReport"
                            :prop-data="objectToTableConsolidated"/>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex'
    import {HalfCircleSpinner} from 'epic-spinners'
    import ManagerGroupSelect from "../../partials/Form/GroupSelect/ManagerGroupSelect";
    import TableConsolidated from "./Table/TableConsolidated";
    import TableReport from "./TableReport/TableReport";
    import TableFilterReport from "./TableFilterReport/TableFilterReport";

    export default {
        components: {
            TableFilterReport,
            TableReport,
            TableConsolidated,
            ManagerGroupSelect,
            HalfCircleSpinner
        },
        name: "main-consolidated",
        data() {
            return {
                state: false,
                is_first: true,
                objectToTableConsolidated: {
                    params: {},
                    asignatures: [],
                    enrollments: [],
                },
                objectToManagerGroupSelect: {
                    isSubGroup: false,
                    referenceId: "statistics",
                    referenceToReciveObjectSelected: 'to-receive-object-selected@' + this.referenceId + '.managerGroupSelect',
                },
            }
        },
        created() {
            this.managerEvents()
            this.initToast()
        },
        computed: {
            ...mapState([
                'institutionOfTeacher',
                'periodsworkingday'
            ]),
        },
        methods: {
            initToast() {
                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
            },

            managerEvents() {
                //Se subscribe al evento generado por menu-statistics, este le permite saber si se debe
                //mostrar la sección de consolidado con su respectiva consulta, ya que este evento devuelve
                //un objeto con los datos seleccionados de manager-group-select
                this.$bus.$on('SelectedFieldsEvent@MenuStatistics', objectMenuStatistics => {
                    this.objectToTableConsolidated.params = objectMenuStatistics
                    this.managerSelectedFieldsEvent(objectMenuStatistics)

                })

                // Se subscribe al evento generado por menu-statistics, para generar una nueva consulta de consolidados
                // si algún select de manager-group-select fue modificado

                this.$bus.$on('SelectedCurrentView@MenuStatistics', objectMenuStatistics => {
                    this.objectToTableConsolidated.params = objectMenuStatistics
                    this.managerSelectedFieldsEvent(this.objectToTableConsolidated.params)
                })
            },

            managerSelectedFieldsEvent(objectMenuStatistics) {
                if (this.$store.state.currentView == 'main-consolidated') {
                    this.getWhoTriggered(objectMenuStatistics)
                }
            },

            getWhoTriggered(objectMenuStatistics) {
                let whoTriggered = objectMenuStatistics.eventInformation.whoTriggered

                if (
                    whoTriggered == 'pdf' ||
                    whoTriggered == 'areas' ||
                    whoTriggered == 'excel' ||
                    whoTriggered == 'componentManagerGroupSelect' ||
                    whoTriggered == 'save-report-final' ||
                    whoTriggered == 'check-filter-report' ||
                    whoTriggered == 'areas-final') {
                    this.managerQueryForFilterConsolidated(objectMenuStatistics)
                }
                console.log(objectMenuStatistics)
                console.log(this.objectToTableConsolidated)
            },

            managerQueryForFilterConsolidated(objectMenuStatistics) {

                let params = {
                    grade_id: objectMenuStatistics.objectValuesManagerGroupSelect.grade_id,
                    group_id: objectMenuStatistics.objectValuesManagerGroupSelect.group_id,
                    periods_id: objectMenuStatistics.objectValuesManagerGroupSelect.periods_id,
                    condition: objectMenuStatistics.objectValuesManagerGroupSelect.condition,
                    condition_number: objectMenuStatistics.objectValuesManagerGroupSelect.condition_number,


                    is_filter_areas: objectMenuStatistics.filter.isAreas,
                    is_filter_all_groups: objectMenuStatistics.filter.isAllGroups,
                    is_accumulated: objectMenuStatistics.filter.isAcumulatedPeriod,
                    is_reprobated: objectMenuStatistics.filter.isReprobated,
                    is_filter_report: objectMenuStatistics.filter.isFilterReport,
                    is_report: objectMenuStatistics.filter.isReport,
                    is_areas_final: objectMenuStatistics.filter.isAreasFinal,

                    url_subjects: '',
                    url_consolidated: '',
                    type_response: objectMenuStatistics.eventInformation.whoTriggered,
                }
                params.url_subjects = '/ajax/getSubjects'
                params.url_consolidated = '/ajax/getTableConsolidated'

                this.getContentConsolidated(params)
            },

            getContentConsolidated(params) {
                // Petición para obetener la cabecera de la tabla (areas o asignaturas)
                axios.get(params.url_subjects, {params}).then(res => {
                    this.objectToTableConsolidated.asignatures = res.data
                    //Si petición anterior es ok
                    this.dispatcherConsolidated(params)
                })
            },

            // Método que trae todos los datos sobre el consolidado a consultar
            dispatcherConsolidated(params) {
                switch (params.type_response) {
                    case 'pdf':
                        this.executePDF(params)
                        break;
                    case 'excel':
                        toastr.success('En desarrollo...')
                        break;
                    case 'save-report-final':
                        this.executeReport(params)
                        break;
                    case 'report-final':
                        console.log('hi')
                        this.executeReport(params)
                        break;
                    default:
                        this.executeDefault(params)
                }
            },

            executePDF(params) {
                var esc = encodeURIComponent;
                var query = Object.keys(params)
                    .map(k => esc(k) + '=' + esc(params[k]))
                    .join('&');
                let url = params.url_consolidated + '?' + query;
                window.open(url);
            },

            executeDefault(params) {
                this.state = false
                this.is_first = false
                axios.get(params.url_consolidated, {params}).then(res => {
                    // Cuando la variable local tiene la información, le asignamos valor true a la variable
                    // state, para que renderice el componente table-consolidated
                    if (res.status == 200) {
                        this.objectToTableConsolidated.enrollments = res.data
                        this.state = true
                    }
                }).catch(error => {
                    this.is_first = true
                })
            },
            executeReport(params) {
                this.state = false
                this.is_first = false

                let data = {
                    'areas': params.is_filter_areas,
                    'condition': params.condition,
                    'trigger': params.type_response,
                    'condition_number': params.condition_number,
                    'is_report_final': params.is_filter_report,
                    'is_report_asignatures': params.is_report,
                    'asignatures': this.objectToTableConsolidated.asignatures,
                    'enrollments': this.objectToTableConsolidated.enrollments,
                }

                let _this = this
                axios.post('/ajax/FinalReport/dispatchers', {data})
                    .then(response => {
                        if (response.status == 200) {
                            _this.state = true
                            toastr.success('Operación exitosa..!')
                            console.log(response.data)
                        }
                    })
                    .catch(function (error) {
                        _this.is_first = true
                    });
            },
        },
        destroyed() {
            this.$bus.$off('SelectedCurrentView@MenuStatistics')
            this.$bus.$off('SelectedFieldsEvent@MenuStatistics')
        }

    }
</script>

<style scoped>

</style>