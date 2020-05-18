<template>
  <div id="app">
    <v-app style="background-color: #f2f2f2">
      <v-content>
        <v-row>
          <v-col>
            <v-card height="60" class="pa-4">
              <div class="d-flex align-center justify-space-between">
                <v-menu offset-y close-on-click close-on-content-click>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      color="primary"
                      dark
                      v-on="on"
                      small
                    >
                      Действия
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item @click="getResources">
                      Обновить данные
                    </v-list-item>
                    <v-list-item @click="openMassEditDialog">
                      Изменить ресурсы
                    </v-list-item>
                    <resource-duplicate-dialog :resources="resourcesSelected" @success="getResources">
                      <template v-slot:activator="{ on }">
                        <v-list-item v-on="on">
                          Копировать ресурсы
                        </v-list-item>
                      </template>
                    </resource-duplicate-dialog>
                    <field-replace-dialog :resources="resourcesSelected" @success="getResources">
                      <template v-slot:activator="{ on }">
                        <v-list-item v-on="on">
                          Замена значения в поле
                        </v-list-item>
                      </template>
                    </field-replace-dialog>
                  </v-list>
                </v-menu>
                <v-btn @click="openMassEditDialog" small>
                  <v-icon small class="me-2">
                    mdi-pencil
                  </v-icon>
                  Изменить
                </v-btn>
                <v-menu offset-y :close-on-content-click="false">
                  <template v-slot:activator="{ on }">
                    <v-btn
                      color="secondary"
                      dark
                      v-on="on"
                      small
                    >
                      Фильтры
                    </v-btn>
                  </template>
                  <v-card class="pa-4">
                    <v-row>
                      <v-col>
                        Фильтры <v-btn x-small dark color="primary" @click="addFilter" class="ml-3">
                        <v-icon x-small>mdi-plus</v-icon>
                      </v-btn>
                      </v-col>
                    </v-row>
                    <v-row v-for="(f, i) in filters" :key="f.field">
                      <v-col class="py-0">
                        <v-autocomplete
                          label="Поле"
                          dense
                          :items="fields"
                          item-text="text"
                          item-value="value"
                          v-model="filters[i].field"
                        ></v-autocomplete>
                      </v-col>
                      <v-col class="py-0">
                        <v-select
                          label="Оператор"
                          dense
                          :items="operators"
                        v-model="filters[i].operator"></v-select>
                      </v-col>
                      <v-col class="py-0">
                        <v-text-field
                          label="Значение"
                          dense
                          v-model="filters[i].value"
                          @keyup.enter="getResources"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="auto">
                        <v-switch
                          v-model="f.active"
                          class="mt-0 pt-0"
                          dense
                        ></v-switch>
                      </v-col>
                      <v-col cols="auto">
                        <v-icon @click="filters.splice(filters.indexOf(f), 1)">mdi-trash-can-outline</v-icon>
                      </v-col>
                    </v-row>
                  </v-card>
                </v-menu>
              </div>
            </v-card>
          </v-col>
          <v-col>
            <v-card height="60" class="pa-3">
              <v-select
                :items="contexts"
                item-text="name"
                item-value="key"
                v-model="contextFilter"
                hide-details
                class="ma-0 pa-0"
                placeholder="Контекст"
                style="font-size: 12px;"
                clearable
                @change="refresh"
                @click:clear="refresh"
              ></v-select>
            </v-card>
          </v-col>
          <v-col>
            <v-card height="60" class="pa-3">
              <v-select
                :items="templates"
                item-text="templatename"
                item-value="id"
                v-model="templateFilter"
                hide-details
                class="ma-0 pa-0"
                placeholder="Шаблон"
                style="font-size: 12px"
                clearable
                @change="refresh"
                @click:clear="refresh"
              ></v-select>
            </v-card>
          </v-col>
          <v-col>
            <v-card height="60" class="pa-3">
              <div class="d-flex align-center">
                <v-text-field
                  placeholder="Поиск"
                  hide-details
                  class="pt-0 mt-0 mr-1"
                  clearable
                  v-model="searchField"
                  :disabled="!!filters.length"
                  @click:clear="refresh"
                  @keydown.enter="getResources"
                  style="font-size: 12px"
                ></v-text-field>
                <v-btn class="primary" dark @click="getResources" small>
                  <v-icon>mdi-magnify</v-icon>
                </v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <v-card class="my-4">
          <v-card class="ma-0 pt-1" flat style="font-size: 11px;" id="rebatcher-before-resource-table">
            <v-row>
              <v-col class="py-0" cols="auto">
                <div class="d-flex align-center px-4 py-2 fill-height">
                  Выбрано: {{resourcesSelected.length}}
                </div>
              </v-col>
              <v-col class="py-0">
                <div class="d-flex align-center px-4 py-2 fill-height">
                  <div style="white-space: nowrap;" class="mx-3">
                    <v-autocomplete
                      label="Поля"
                      v-model="fieldsSelected"
                      :items="fields"
                      multiple
                      style="font-size: inherit;"
                      hide-details
                      dense
                      return-object
                    >
                      <template v-slot:selection="{item, index}">
                        <div v-if="index === 0" class="mr-4">
                          Выбрано: {{fieldsSelected.length}}
                        </div>
                      </template>
                    </v-autocomplete>
                  </div>
                  <div style="white-space: nowrap;" class="mx-3">
                    <v-autocomplete
                      label="TV-поля"
                      v-model="tvsSelected"
                      :items="tvs"
                      :hint="tvsSelected.map(tv => tv.caption).join(', ')"
                      item-text="name"
                      multiple
                      persistent-hint
                      return-object
                      style="font-size: inherit;"
                      hide-details
                      dense
                      @blur="getResources"
                    >
                      <template v-slot:selection="{item, index}">
                        <div v-if="index === 0">
                          Выбрано: {{tvsSelected.length}}
                        </div>
                      </template>
                    </v-autocomplete>
                  </div>
                </div>
              </v-col>
              <v-col class="py-0">
                <div class="d-flex justify-end align-center px-4 py-2 fill-height">
                  <div style="white-space: nowrap;" class="mx-3">Всего: {{pagination.total}}</div>
                  <v-text-field
                    style="max-width: 120px; font-size: inherit;"
                    class="align-center mt-0"
                    dense
                    :value="pagination.itemsPerPage"
                    @change="changeItemsPerPage"
                    hide-details
                  >
                    <template v-slot:prepend>
                      <div style="white-space: nowrap;">На странице:</div>
                    </template>
                  </v-text-field>
                </div>
              </v-col>
            </v-row>
            <v-divider class="my-1"></v-divider>
          </v-card>
          <v-data-table id="rebatcher-resource-table"
                        :loading="loading"

                        v-model="resourcesSelected"
                        :headers="tableHeaders"
                        :items="resources"

                        :page.sync="pagination.page"
                        :items-per-page.sync="pagination.itemsPerPage"
                        :server-items-length="pagination.total"
                        :sort-by.sync="sort.sortBy"
                        :sort-desc.sync="sort.sortDesc"

                        @page-count="pagination.pageCount = $event"
                        @update:page="getResources"
                        @update:items-per-page="getResources"
                        @update:sort-by="getResources"
                        @update:sort-desc="getResources"

                        show-select
                        dense
          >
            <template v-slot:item.id="{ item }">
              <a :href="'/manager/?a=resource/update&id=' + item.id" target="_blank">{{item.id}}</a>
            </template>
            <template
              v-for="f in fieldsEditable"
              v-slot:[dynamicSlotName(f.value)]="{ item }"
            >
              <field-edit-dialog :key="f.value" :field="f" :item="item"></field-edit-dialog>
            </template>
            <template
              v-for="t in tvsSelected"
              v-slot:[dynamicSlotName(`tv_${t.name}`)]="{ item }"
            >
              <tv-edit-dialog :key="t.name" :tv="t" :item="item"></tv-edit-dialog>
            </template>
            <template v-slot:item.btn_view="{ item }">
              <a :href="'/' + item.uri" target="_blank" style="text-decoration: none;">
                <v-tooltip left>
                  <template v-slot:activator="{ on }">
                    <v-icon dense v-on="on">mdi-eye-outline</v-icon>
                  </template>
                  {{"/" + item.uri}}
                </v-tooltip>
              </a>
            </template>
          </v-data-table>
        </v-card>

        <v-dialog v-model="dialogs.massEditDialog.active" max-width="600">
          <v-card class="pa-4">
            <v-card-title>Изменить ресурсы</v-card-title>
            <v-card-text>
              <div class="my-6">
                <div class="d-flex justify-space-between align-center">
                  <div class="display-1">Поля</div>
                  <v-icon @click="addMassEditField">mdi-plus</v-icon>
                </div>
                <div v-for="f in dialogs.massEditDialog.fields" :key="f.field">

                  <v-row>
                    <v-col class="py-2">
                      <v-autocomplete
                        label="Поле"
                        :items="fields"
                        v-model="f.field"
                        item-text="value"
                        hide-details
                        dense
                      ></v-autocomplete>
                    </v-col>
                    <v-col class="py-2">
                      <v-text-field
                        label="Значение"
                        v-model="f.value"
                        hide-details
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="auto">
                      <v-icon @click="dialogs.massEditDialog.fields.splice(dialogs.massEditDialog.fields.indexOf(f), 1)">mdi-trash-can-outline</v-icon>
                    </v-col>
                  </v-row>

                </div>
              </div>
              <div class="my-6">
                <div class="d-flex justify-space-between align-center">
                  <div class="display-1">Tv-поля</div>
                  <v-icon @click="addMassEditTv">mdi-plus</v-icon>
                </div>
                <div v-for="tv in dialogs.massEditDialog.tvs" :key="tv.field">
                  <v-row>
                    <v-col class="py-2">
                      <v-autocomplete
                        label="Tv-поле"
                        :items="tvs"
                        v-model="tv.field"
                        item-text="name"
                        item-value="id"
                        hide-details
                        dense
                      ></v-autocomplete>
                    </v-col>
                    <v-col class="py-2">
                      <v-text-field
                        label="Значение"
                        v-model="tv.value"
                        hide-details
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="auto">
                      <v-icon @click="dialogs.massEditDialog.tvs.splice(dialogs.massEditDialog.tvs.indexOf(tv), 1)">mdi-trash-can-outline</v-icon>
                    </v-col>
                  </v-row>

                </div>
              </div>
              <div class="mt-6">
                <v-btn
                  block
                  color="primary"
                  dark
                  @click="sendMassEditForm"
                >Отправить
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-dialog>

        <v-snackbar
          v-model="snackbar.active"
          :color="snackbar.color"
          :timeout="2000"
        >{{this.snackbar.text}}
        </v-snackbar>
      </v-content>
    </v-app>
  </div>
</template>

<script>
    import axios from "axios";
    import resourceFields from "@/resourceFields";
    import sqlOperators from "@/sqlOperators";

    import ResourceDuplicateDialog from "@/components/ResourceDuplicateDialog";
    import FieldEditDialog from "@/components/FieldEditDialog";
    import FieldReplaceDialog from "@/components/FieldReplaceDialog";
    import TvEditDialog from "@/components/TvEditDialog";

    export default {
        name: 'App',
        components: {
            ResourceDuplicateDialog,
            FieldReplaceDialog,
            FieldEditDialog,
            TvEditDialog
        },
        data() {
            return {
                loading: false,
                searchField: "",
                templateFilter: null,
                contextFilter: null,
                headers: [
                    {text: 'id', value: 'id', width: '75', align: 'center'},
                    {text: 'tpl', value: 'template', width: '75', align: 'center'},
                    {text: 'pagetitle', value: 'pagetitle'}
                ],

                resources: [],
                resourcesSelected: [],
                fields: resourceFields.all,
                fieldsSelected: resourceFields.default,
                operators: sqlOperators,
                tvs: [],
                tvsSelected: [],
                // templates: [],
                // contexts: [],

                filters: [],

                pagination: {
                    page: 1,
                    pageCount: 0,
                    itemsPerPage: 10,
                    total: null
                },
                sort: {
                    sortBy: [],
                    sortDesc: []
                },
                dialogs: {
                    massEditDialog: {
                        active: false,

                        fields: [],
                        tvs: [],

                        addValue: false,
                        addDelimiter: "||"
                    }
                },
                snackbar: {
                    active: false,
                    text: "",
                    color: ""
                }
            }
        },
        computed: {
            tableHeaders() {
                var fields = this.fieldsSelected;
                var tvColumns = this.tvsSelected.map(tv => {
                    return {
                        text: tv.caption ? tv.caption : tv.name,
                        value: 'tv_' + tv.name
                    }
                });
                var btn_view = [{text: '', value: 'btn_view', width: "50"}];
                return fields.concat(tvColumns, btn_view);
            },
            fieldsEditable() {
                return this.fieldsSelected.filter(f => !f.immutable);
            },
            contexts() {
                return this.$store.getters.contexts;
            },
            templates() {
                return this.$store.getters.templates;
            }
        },
        methods: {
            dynamicSlotName(f) {
                return `item.${f}`;
            },
            log(l) {
                console.log(l);
            },
            getResources() {
                return new Promise(resolve => {
                    this.loading = true;
                    this.resourcesSelected = [];
                    let page = this.pagination.page,
                        itemsPerPage = this.pagination.itemsPerPage;
                    var params = {
                        limit: itemsPerPage !== -1 ? itemsPerPage : 0,
                        offset: page > 0 ? page - 1 : 0
                    };
                    if (this.sort.sortBy.length) {
                        params.sort = [];
                        this.sort.sortBy.forEach((s, i) => {
                            params.sort.push([s, this.sort.sortDesc[i]]);
                        })
                    }

                    if (this.filters.length) {
                        params.filters = this.filters.filter(f => f.active);
                    } else if (this.searchField?.length) {
                        params.search = this.searchField;
                    }
                    if (this.contextFilter?.length) {
                        params.context_key = this.contextFilter;
                    }
                    if (this.templateFilter !== null) {
                        params.template = this.templateFilter;
                    }
                    if (this.tvsSelected.length) {
                        params.tvs = this.tvsSelected.map(tv => tv.name);
                    }

                    axios.post(`/assets/components/rebatcher/connectors/resources.php`, {
                        data: params
                    })
                        .then(r => {
                            this.resources = r.data.data;
                            this.pagination.total = r.data.total;
                            this.loading = false;
                            resolve();
                        });
                });
            },
            getTVs() {
                return new Promise(resolve => {
                    axios.get('/assets/components/rebatcher/connectors/tvs.php')
                        .then(r => {
                            this.tvs = r.data;
                            resolve();
                        });
                });
            },
            addFilter() {
                this.filters.push({
                    active: true,
                    field: '',
                    operator: '',
                    value: ''
                })
            },
            openMassEditDialog() {
                if (!this.resourcesSelected.length) {
                    this.snackbar.active = true;
                    this.snackbar.color = 'error';
                    this.snackbar.text = 'Не выбраны ресуры';
                    return;
                }
                this.dialogs.massEditDialog.active = true;
            },
            addMassEditField() {
                this.dialogs.massEditDialog.fields.push({
                    field: '',
                    value: ''
                })
            },
            addMassEditTv() {
                this.dialogs.massEditDialog.tvs.push({
                    field: '',
                    value: ''
                })
            },
            sendMassEditForm() {
                // eslint-disable-next-line no-unused-vars
                var arr = this.resourcesSelected.map(r => {
                    return {
                        id: r.id,
                        fields: this.dialogs.massEditDialog.fields,
                        tvs: this.dialogs.massEditDialog.tvs,
                    }
                });
                let data = {
                    data: arr
                };
                axios.post('/assets/components/rebatcher/connectors/batch_update.php', data)
                    .then(r => {
                        console.log(r.data);
                        this.dialogs.massEditDialog.active = false;
                        this.getResources();
                    });

            },
            changeItemsPerPage(v) {
                var page = parseInt(v);
                if (!page) return;
                this.pagination.itemsPerPage = page;
            },
            refresh() {
                setTimeout(() => this.getResources(), 150);
            }
        },
        async mounted() {
            window.rebatcherApp = this;
            await this.$store.dispatch('init');
            await this.getTVs();
            await this.getResources();
            // await this.getTemplates();
            // await this.getContexts();
        }
    }
</script>

<style lang="sass">
  html
    font-size: 14px !important

    #rebatcher-before-resource-table
      .v-label
        font-size: inherit
</style>
