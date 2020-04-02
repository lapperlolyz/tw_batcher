<template>
  <div id="app">
    <v-app style="background-color: #f2f2f2">
      <v-content>
        <v-card class="my-4 pa-3">
          <v-row>
            <v-col>
              <v-select
                label="TV-поля"
                v-model="tvsSelected"
                :items="tvs"
                :hint="tvsSelected.map(tv => tv.caption).join(', ')"
                item-text="name"
                multiple
                persistent-hint
                return-object
                style="font-size: 12px;"
                hide-details
                dense
              ></v-select>
            </v-col>
            <v-col></v-col>
            <v-col></v-col>
          </v-row>
        </v-card>

        <v-row>
            <v-col>
              <v-card height="60" class="pa-4">
                <div class="d-flex align-center justify-space-between">
                    <v-menu offset-y>
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
                        <v-list-item @click="openMassEditTvDialog">
                          Переключить TV
                        </v-list-item>
                      </v-list>
                    </v-menu>
                    <v-btn @click="openMassEditTvDialog" small>
                      <v-icon small class="me-2">
                        mdi-pencil
                      </v-icon>
                      TV
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
                            Фильтры
                          </v-col>
                          <v-col>
                            <v-btn x-small dark color="primary">
                              <v-icon x-small>mdi-plus</v-icon>
                            </v-btn>
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
          <v-data-table
            :loading="loading"
            v-model="resourcesSelected"
            show-select
            :headers="tableHeaders"
            :items="resources"
            id="rebatcher-resource-table"
            :page.sync="pagination.page"
            :items-per-page.sync="pagination.itemsPerPage"
            :server-items-length="pagination.total"
            @page-count="pagination.pageCount = $event"
            @update:page="getResources"
            @update:items-per-page="getResources"
            dense
          >
<!--            <template v-slot:body="{ items, headers }">-->
<!--              <tbody>-->
<!--              <tr v-for="(item,idx) in items" :key="idx">-->
<!--                <td>-->
<!--                  <v-checkbox @click="log(item)"></v-checkbox>-->
<!--                </td>-->
<!--                <td v-for="(header,key) in headers" :key="key">-->
<!--                  <v-edit-dialog-->
<!--                          :return-value.sync="item[header.value]"-->
<!--                  > {{item[header.value]}}-->
<!--                    <template v-slot:input>-->
<!--                      <v-text-field-->
<!--                              v-model="item[header.value]"-->
<!--                              label="Edit"-->
<!--                              single-line-->
<!--                      ></v-text-field>-->
<!--                    </template>-->
<!--                  </v-edit-dialog>-->
<!--                </td>-->
<!--              </tr>-->
<!--              </tbody>-->
<!--            </template>-->
<!--            <template v-for="h in headers" v-slot:[h.value]="props">-->
<!--              <v-edit-dialog-->
<!--                :key="h.value"-->
<!--                :return-value.sync="props.item[h.value]"-->
<!--                @open="log(props)"-->
<!--              >-->
<!--                {{props.item[h.value]}}-->
<!--                <template v-slot:input>-->
<!--                  <v-text-field-->
<!--                          label="Значение"-->
<!--                          single-line-->
<!--                          counter-->
<!--                  ></v-text-field>-->
<!--                  <v-btn></v-btn>-->
<!--                </template>-->
<!--              </v-edit-dialog>-->
<!--            </template>-->
          </v-data-table>
        </v-card>

        <v-dialog v-model="dialogs.massTvEditDialog.active" max-width="500">
          <v-card class="pa-4">
            <v-row>
              <v-col>
                <v-select
                  label="Tv-поле"
                  :items="tvs"
                  v-model="dialogs.massTvEditDialog.selected"
                  item-text="name"
                  return-object
                  hide-details
                ></v-select>
              </v-col>
              <v-col>
                <v-text-field
                  label="Значение"
                  v-model="dialogs.massTvEditDialog.value"
                  hide-details
                  @keyup.enter="sendMassEditTvForm"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-checkbox label="Добавить значение к текущему" disabled></v-checkbox>
            <v-btn
              block
              color="primary"
              dark
              @click="sendMassEditTvForm"
            >Отправить</v-btn>
          </v-card>
        </v-dialog>
        <v-snackbar
                v-model="snackbar.active"
                :color="snackbar.color"
                :timeout="2000"
        >{{this.snackbar.text}}</v-snackbar>
      </v-content>
    </v-app>
  </div>
</template>

<script>
    import axios from "axios";

    export default {
        name: 'App',
        components: {},
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
                tvs: [],
                tvsSelected: [],
                templates: [],
                contexts: [],

                pagination: {
                    page: 1,
                    pageCount: 0,
                    itemsPerPage: 10,
                    total: null
                },
                dialogs: {
                    massTvEditDialog: {
                        active: false,
                        selected: [],
                        value: ""
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
                var tvColumns = this.tvsSelected.map(tv => {
                   return {
                       text: tv.caption ? tv.caption : tv.name,
                       value: 'tv-'+tv.name
                   }
                });
                return this.headers.concat(tvColumns);
            }
        },
        methods: {
            log(l) {
                console.log(l);
            },
            getResources() {
                return new Promise(resolve => {
                    this.loading = true;
                    this.resourcesSelected = [];
                    let page = this.pagination.page,
                        // pageCount = this.pagination.pageCount,
                        itemsPerPage = this.pagination.itemsPerPage;
                    var params = {
                        limit: itemsPerPage !== -1 ? itemsPerPage : 0,
                        offset: page > 0 ? page - 1 : 0
                    };
                    if (this.searchField?.length) {
                        params.search = this.searchField;
                    }
                    if (this.contextFilter?.length) {
                        params.context_key = this.contextFilter;
                    }
                    if (this.templateFilter !== null) {
                        params.template = this.templateFilter;
                    }
                    axios.get(`/assets/components/rebatcher/connectors/resources.php`, {
                        params: params
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
            getTemplates() {
                return new Promise(resolve => {
                    axios.get('/assets/components/rebatcher/connectors/templates.php')
                        .then(r => {
                            this.templates = [{id: 0, templatename: 'Не задан'}].concat(r.data);
                            resolve();
                        });
                });
            },
            getContexts() {
                return new Promise(resolve => {
                    axios.get('/assets/components/rebatcher/connectors/contexts.php')
                        .then(r => {
                            this.contexts = r.data;
                            resolve();
                        });
                });
            },
            openMassEditTvDialog() {
                if (!this.resourcesSelected.length) {
                    this.snackbar.active = true;
                    this.snackbar.color = 'error';
                    this.snackbar.text = 'Не выбраны ресуры';
                    return;
                }
                this.dialogs.massTvEditDialog.active = true;
            },
            sendMassEditTvForm() {
                // eslint-disable-next-line no-unused-vars
                var arr = this.resourcesSelected.map(r => {
                    return {
                        id: r.id,
                        tvId: this.dialogs.massTvEditDialog.selected.id,
                        value: this.dialogs.massTvEditDialog.value
                    }
                });
                // axios.post('/assets/components/rebatcher/connectors/tvs_mass_update.php', {
                //     params: {
                //         data: arr
                //     }
                // })
                let data = {
                    data: arr
                };
                axios.post('/assets/components/rebatcher/connectors/tvs_mass_update.php', JSON.stringify(data))
                .then(r => {
                  console.log(r.data);
                  this.dialogs.massTvEditDialog.active = false;
                  this.getResources();
                });

            },
            refresh() {
                setTimeout(() => this.getResources(), 150);
            }
        },
        async mounted() {
            window.rebatcherApp = this;
            await this.getTVs();
            await this.getResources();
            await this.getTemplates();
            await this.getContexts();
        }
    }
</script>

<style lang="sass">
  html
    font-size: 14px !important
  #rebatcher-resource-table
    table
      table-layout: fixed
</style>
