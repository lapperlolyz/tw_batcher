<template>
  <v-dialog max-width="640" v-model="active">
    <template v-slot:activator="{ on }">
      <slot :on="on" name="activator"></slot>
    </template>
    <v-card>
      <v-card-title>Замена</v-card-title>
      <v-card-text>
        <v-row>
          <v-col>
            <v-autocomplete
              label="Поле"
              dense
              outlined
              :items="fields"
              item-text="text"
              item-value="value"
              v-model="selectedField"
              hide-details
            ></v-autocomplete>
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <v-textarea
              v-model="find"
              label="Что заменить"
              dense
              outlined
              hide-details
            ></v-textarea>
            <v-row>
              <v-col>
                <v-checkbox
                  label="Регулярное выражение"
                  v-model="isRegExp"
                  hide-details
                ></v-checkbox>
              </v-col>
              <v-col>
                <v-checkbox
                  label="Учитывать регистр"
                  v-model="isCaseSensitive"
                  :disabled="isRegExp"
                  hide-details
                ></v-checkbox>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <v-textarea
              v-model="replace"
              label="На что заменить"
              dense
              outlined
              hide-details
            ></v-textarea>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="primary" text @click="submit" :disabled="!valid">Применить</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
    import axios from "axios";
    import resourceFields from "@/resourceFields";
    export default {
        name: "FieldReplaceDialog",
        props: {
            resources: {
                required: true
            }
        },
        data() {
            return {
                active: false,
                find: '',
                isRegExp: false,
                isCaseSensitive: true,
                replace: '',
                fields: resourceFields.all,
                selectedField: undefined
            }
        },
        computed: {
            valid() {
                return this.find.length > 0
                    && this.selectedField
                    && this.resources.length
                    && (this.isRegExp ? this.find[0] === '/' : true);
            }
        },
        methods: {
            submit() {
                axios.post('/assets/components/rebatcher/connectors/findreplace.php', {
                    data: {
                        resources: this.resources.map(r => r.id),
                        field: this.selectedField,
                        find: this.find,
                        replace: this.replace,
                        is_regexp: this.isRegExp,
                        is_case_sensitive: this.isCaseSensitive,
                        confirm: false
                    }
                })
                    .then(() => {
                        this.active = false;
                        this.$emit('success');
                    });

            }
        },
    }
</script>

<style scoped>

</style>