<template>
  <v-dialog max-width="320" v-model="active">
    <template v-slot:activator="{ on }">
      <slot :on="on" name="activator"></slot>
    </template>
    <v-card>
      <v-card-title>Копирование ресурсов</v-card-title>
      <v-card-text>
        <v-row>
          <v-col>
            <v-list dense>
              <v-card class="mb-1" tile :key="r.id" v-for="r in resources">
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>{{r.pagetitle}}</v-list-item-title>
                    <v-list-item-subtitle>#{{r.id}}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-card>
            </v-list>
          </v-col>
        </v-row>
        <v-divider class="my-3"></v-divider>
        <v-row>
          <v-col>
            <v-text-field
              v-model="parent"
              label="Цель"
              dense
              outlined
            ></v-text-field>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="primary" text :disabled="parent.length === 0" @click="submit">Применить</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
    import axios from "axios";
    export default {
        name: "ResourceDuplicateDialog",
        props: {
            resources: {
                type: Array,
                required: true
            },
        },
        data() {
            return {
                active: false,
                parent: ''
            }
        },
        methods: {
            submit() {
                axios.post('/assets/components/rebatcher/connectors/duplicate.php', {
                    resources: this.resources.map(r => r.id),
                    parent: parseInt(this.parent)
                })
                    .then(() => {
                        this.active = false
                        this.parent = '';
                        this.$emit('success');
                    });

            }
        },
    }
</script>

<style scoped>

</style>