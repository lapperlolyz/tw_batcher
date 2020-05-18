<template>
    <v-menu
      v-model="active"
      :close-on-content-click="false"
      :close-on-click="false"
    >
      <template v-slot:activator="{ on }">
        <span v-on="on" class="column-value">{{oldValue}}</span>
      </template>
      <v-card>
        <v-card-title>
          Изменить поле {{field.text || field.value}}
        </v-card-title>
        <v-card-text>
          <template v-if="field.type === 'list' && field.binding !== undefined">
            <v-autocomplete
              v-model="value"
              :items="$store.getters[field.binding]"
              :item-text="field.itemText"
              :item-value="field.itemValue"
            ></v-autocomplete>
          </template>
          <template v-else>
            <v-text-field v-model="value" label="Значение"></v-text-field>
          </template>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="cancel">Отмена</v-btn>
          <v-btn text color="primary" @click="submit" :disabled="value === oldValue">Сохранить</v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
</template>

<script>
  import axios from "axios";
    export default {
        name: "FieldEditDialog",
        props: {
            field: {
                type: Object,
                required: true
            },
            item: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                active: false,
                value: '',
                oldValue: ''
            }
        },
        computed: {
            formData() {
                return {
                    data: [{
                        id: this.item.id,
                        fields: [{
                            field: this.field.value,
                            value: this.value
                        }]
                    }]
                };
            }
        },
        methods: {
            setValue() {
                let val = this.item[this.field.value];
                this.value = val;
                this.oldValue = val;
            },
            submit() {
                axios.post('/assets/components/rebatcher/connectors/batch_update.php', this.formData)
                    .then(() => {
                        this.oldValue = this.value;
                        this.active = false;
                        this.$emit('success');
                        // console.log(r.data);
                        // this.dialogs.massEditDialog.active = false;
                        // this.getResources();
                    });
            },
            cancel() {
                this.value = this.oldValue;
                this.active = false;
            }
        },
        watch: {
            item() {
                this.setValue();
            }
        },
        mounted() {
            this.setValue();
            // let val = this.item[this.field.value];
            // this.value = val; this.oldValue = val;
        },
    }
</script>

<style lang="sass" scoped>
  .column-value
    cursor: pointer
    min-width: 25px
    min-height: 15px
    display: inline-block
    &:hover
      color: #1a237e
</style>