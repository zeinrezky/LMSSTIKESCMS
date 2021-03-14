<template>
<div>
  <b-row>
    <b-colxx xxs="12">
      <piaf-breadcrumb :heading="'User Management'" />
      <div class="separator mb-5"></div>
    </b-colxx>
  </b-row>
  <b-row>
    <b-colxx xxs="12">
      <b-card class="mb-4">
        <b-row class="mb-3 d-flex align-items-end">
          <b-col sm="12" md=3>
            <b-form-group label="Search" label-cols-sm="12" label-align-sm="left" label-size="sm" class="mb-0">
              <b-input-group size="sm">
                <b-form-input v-model="paging.search" type="search" placeholder="Type to Search"></b-form-input>
                <b-input-group-append>
                  <b-button :disabled="!paging.search" @click="paging.search = ''" variant="dark">Clear</b-button>
                </b-input-group-append>
              </b-input-group>
            </b-form-group>
          </b-col>
          <b-col sm=12 md=9 class="text-right">
            <b-button size="sm" variant="primary" squared v-b-modal.modal-form>
              <b-icon icon="plus"></b-icon> Add New
            </b-button>
          </b-col>
        </b-row>
        <!-- Main table element -->
        <b-overlay :show="table.loading" rounded="sm">
          <b-table show-empty small stacked="md" :items="table.data" :fields="table.field" :current-page="paging.currentPage" :sort-by.sync="paging.sortBy" :sort-direction="paging.sortDirection" :sort-desc.sync="paging.sortDesc" bordered striped head-variant="dark" hover per-page="0">
            <template v-slot:cell(no)="row">
              {{ (paging.currentPage-1) * paging.perPage + row.index + 1 }}
            </template>
            <template v-slot:cell(actions)="row">
              <b-button size="xs" variant="danger" squared @click="doDelete(row.item.id,false)">
                <b-icon icon="trash"></b-icon>
              </b-button>
              <b-button size="xs" variant="info" squared @click="doUpdate(row.item.id)">
                <b-icon icon="pencil"></b-icon>
              </b-button>
            </template>
          </b-table>
        </b-overlay>
        <b-row>
          <b-col xs=12 md=3>
            <b-form-group label="Per page" label-cols-sm="12" label-align-sm="left" label-size="sm" label-for="perPageSelect" class="mb-0">
              <b-form-select v-model="paging.perPage" id="perPageSelect" size="sm" :options="opt.pageOptions"></b-form-select>
            </b-form-group>
          </b-col>
          <b-col xs=12 md=9 class="justify-content-end d-flex align-items-end">
            <b-pagination v-model="paging.currentPage" :total-rows="paging.totalRows" :per-page="paging.perPage" size="sm" class="my-0"></b-pagination>
          </b-col>
        </b-row>
        <!-- EOF Main table element -->
        <!-- Modal Form -->
        <b-modal size="xl" title="Input User" hide-footer centered id="modal-form">
          <b-form @submit.prevent="doSave()">
            <b-row>
              <b-col xs=12 md=4>
                <b-form-group description="click to add or remove image" label="Foto">
                  <label for="form-image" class="w-100 border">
                    <b-img :src="(form.data.image) ? form.data.image : require('../assets/img/default.png')" fluid class="cursor-pointer w-100" />
                    <input type="file" ref="form-image" hidden id="form-image" @change="previewImage" accept="image/*">
                  </label>
                </b-form-group>
              </b-col>
              <b-col xs=12 md=8>
                <b-row>
                  <b-col xs=12 sm=6>
                    <b-form-group label="NIP" :state="!$v.form.data.nip.$error">
                      <b-input v-model="$v.form.data.nip.$model"/>
                    </b-form-group>
                  </b-col>
                  <b-col xs=12 sm=6>
                    <b-form-group label="Nama" :state="!$v.form.data.name.$error">
                      <b-input v-model="$v.form.data.name.$model"/>
                    </b-form-group>
                  </b-col>
                  <b-col xs=12 sm=6>
                    <b-form-group label="Password" :state="!$v.form.data.password.$error">
                      <b-input-group>
                        <b-input v-model="$v.form.data.password.$model" :type="(form.showPass) ? 'text' : 'password'"/>
                        <b-input-group-append>
                          <b-button @click="form.showPass = !form.showPass" variant="light" squared>
                            <b-icon icon="eye"/>
                          </b-button>
                        </b-input-group-append>
                      </b-input-group>
                    </b-form-group>
                  </b-col>
                  <b-col xs=12 sm=6>
                    <b-form-group label="Email" :state="!$v.form.data.email.$error">
                      <b-input v-model="$v.form.data.email.$model"/>
                    </b-form-group>
                  </b-col>
                  <b-col xs=12 sm=6>
                    <b-form-group label="Phone" :state="!$v.form.data.phone.$error">
                      <b-input v-model="$v.form.data.phone.$model"/>
                    </b-form-group>
                  </b-col>
                  <b-col xs=12 sm=6>
                    <b-form-group label="Gender" :state="!$v.form.data.gender.$error">
                      <b-select v-model="$v.form.data.gender.$model" :options="opt.gender"/>
                    </b-form-group>
                  </b-col>
                  <b-col xs=12 sm=6>
                    <b-form-group label="Address" :state="!$v.form.data.address.$error">
                      <b-textarea rows="5" no-resize v-model="$v.form.data.address.$model"/>
                    </b-form-group>
                  </b-col>
                  <b-col xs=12 sm=6>
                    <b-form-group label="Role" :state="!$v.form.data.role.$error">
                      <b-select v-model="$v.form.data.role.$model" :options="opt.role"/>
                    </b-form-group>
                  </b-col>
                </b-row>
              </b-col>
              <b-col sm=12 class="d-flex justify-content-end border-top pt-3">
                <b-button variant="light" squared="" size="md" @click="$bvModal.hide('modal-form')">Cancel</b-button>
                <b-button variant="primary" squared="" type="submit" class="ml-3" :disabled="form.loading">
                  <span class="spinner" v-show="form.loading">
                    <span class="bounce1"></span>
                    <span class="bounce2"></span>
                    <span class="bounce3"></span>
                  </span>
                  <span v-show="!form.loading">Save</span>
                </b-button>
              </b-col>
            </b-row>
          </b-form>
        </b-modal>
        <!-- EOF Modal Form -->
        <!-- Modal Delete -->
        <b-modal id="modal-delete" title="Delete" hide-footer="" hide-header="" size="sm" centered="" body-bg-variant="warning" body-text-variant="light">
          <b-row>
            <b-col sm=12 class="d-flex justify-content-center flex-column align-items-center">
              <h1 style="font-size:50px">
                <b-icon icon="trash-fill" class="border rounded bg-danger p-2" variant="light"></b-icon>
              </h1>
              <h2>Warning</h2>
              <p>Are You Sure to Delete this Data?</p>
            </b-col>
            <b-col sm=12 class="d-flex justify-content-center">
              <b-button variant="light" class="mx-1" @click="$bvModal.hide('modal-delete')">No</b-button>
              <b-button variant="danger" class="mx-1" @click="doDelete(form.deleteId,true)">Yes Delete!</b-button>
            </b-col>
          </b-row>
        </b-modal>
      </b-card>
    </b-colxx>
  </b-row>
</div>
</template>

<script>
import {validationMixin} from 'vuelidate'
import {required} from 'vuelidate/lib/validators'
import {apiUrl} from '../constants/config'
import axios from 'axios'
export default {
  name: 'userManagement',
  mixins: [validationMixin],
  validations: {
    form: {
      data: {
        nip : {required},
        name : {required},
        password : {required},
        email : {required},
        phone : {required},
        address : {required},
        gender : {required},
        role : {required}
      }
    }
  },
  data() {
    return {
      table: {
        field: [{
            key: 'no',
            label: 'No',
            class: 'text-center w-5'
          },
          {
            key: 'nip',
            label: 'NIP',
            sortable: true,
            thClass: 'text-center',
            tdClass: ''
          },
          {
            key: 'name',
            label: 'Name',
            sortable: true,
            thClass: 'text-center',
            tdClass: ''
          },
          {
            key: 'email',
            label: 'Email',
            sortable: true,
            thClass: 'text-center',
            tdClass: ''
          },
          {
            key: 'phone',
            label: 'Phone',
            sortable: true,
            thClass: 'text-center',
            tdClass: ''
          },
          {
            key: 'role',
            label: 'Role',
            sortable: true,
            thClass: 'text-center',
            tdClass: ''
          },
          {
            key: 'actions',
            label: 'Action',
            sortable: true,
            thClass: 'text-center w-10',
            tdClass: 'text-center'
          },
        ],
        data: [],
        loading: false
      },
      form: {
        loading: false,
        data: {
          id : null,
          nip : 123456789876,
          name : 'Administrator',
          password : 123456,
          email : 'administrator@stikes.com',
          phone : '081234567890',
          address : 'Bogor',
          gender : 'Pria',
          image : null,
          role : null,
          status : 'ACTIVE'
          // id : null,
          // nip : null,
          // name : null,
          // password : null,
          // email : null,
          // phone : null,
          // address : null,
          // gender : null,
          // image : null,
          // role : null,
          // status : 'ACTIVE'
        },
        deleteId : null,
        showPass : false
      },
      paging: {
        currentPage: 1,
        perPage: 10,
        sortBy: 'id',
        sortDirection: 'desc',
        sortDesc: true,
        totalRows: 0,
        search: ''
      },
      opt: {
        pageOptions: [10, 20, 50, 100, 200],
        role: ['ADMIN', 'REVIEWER', 'SME'],
        gender: ['Pria', 'Wanita']
      }
    }
  },
  watch: {
    paging : {
      handler(){
        this.doLoad()
      },
      deep: true
    }
  },
  methods: {
    doLoad(){
      let url = `${apiUrl}/api/user`
      let payload = this.paging
      payload.sortDirection = (this.paging.sortDesc) ? 'desc' : 'asc'
      this.table.loading = true
      axios
      .post(url,payload)
      .then((res)=>{
        if(res.data.status){
          this.table.data = res.data.data
        } else {
          this.notif('Error',res.data.msg,'danger')
        }
        this.table.loading = false
      })
      .catch((e)=>{
        this.table.loading = false
        this.notif('Error',e.message,'danger')
      })
    },
    doUpdate(id){
      let url = `${apiUrl}/api/user/get`
      let payload = {'id': id}
      axios
      .post(url,payload)
      .then(res => {
        if (res.data.status) {
          this.form.data = res.data.data
          this.form.data.password = res.data.data.plain_password
          this.$bvModal.show('modal-form')
        } else {
          this.notif('Failed', res.data.msg, 'danger')
        }
      })
      .catch(e => {
        this.notif('Failed', e.message, 'danger')
      });
    },
    doSave(){
      let payload = this.form.data
      let url = `${apiUrl}/api/user/create`
      if (this.form.data.id)
        url = `${apiUrl}/api/user/update`
      this.form.loading = true
      this.$v.form.data.$touch()
      if (this.$v.form.data.$invalid) {
        this.form.loading = false
        this.notif('Warning', 'Please fill required field', 'warning')
      } else {
        axios
        .post(url,payload)
        .then(res => {
          this.form.loading = false
          if (res.data.status) {
            this.doLoad()
            this.clearForm()
            this.notif('Success', res.data.msg, 'success')
          } else {
            this.notif('Failed', res.data.msg, 'danger')
          }
        })
        .catch(e => {
          this.form.loading = false
          this.notif('Failed', e.message, 'danger')
        });
      }
    },
    doDelete(id, doDelete) {
      this.form.deleteId = id
      if (doDelete) {
        let url = `${apiUrl}/api/user/delete`
        let payload = {'id': id}
        axios
        .post(url,payload)
        .then(res => {
          if (res.data.status) {
            this.notif('Success', res.data.msg, 'success')
          } else {
            this.notif('Failed', res.data.msg, 'danger')
          }
          this.$bvModal.hide('modal-delete')
          this.doLoad()
        })
        .catch(e => {
          this.notif('Failed', e.message, 'danger')
        });
      } else {
        this.$bvModal.show('modal-delete')
      }
    },
    previewImage(event) {
      let vm = this
      let theImg = null;
      theImg = event.target.files[0];
      let reader = new FileReader();
      reader.readAsDataURL(theImg);
      reader.onload = function () {
        vm.form.data.image = reader.result
      };
      reader.onerror = function () {
        vm.form.data.image = null
      };
    },
    clearForm() {
      this.$v.form.data.$reset()
      this.form.data = {
        id : null,
        nip : null,
        name : null,
        password : null,
        email : null,
        phone : null,
        address : null,
        gender : null,
        image : null,
        role : null,
        status : 'ACTIVE'
      }
      this.form.deleteId = null
      this.$bvModal.hide('modal-form')
    },
    notif(title, msg, type) {
      this.$bvToast.toast(msg, {
        title: title,
        autoHideDelay: 5000,
        variant: type,
        solid: true,
        toaster: 'b-toaster-bottom-right'
      })
    }
  },
  mounted(){
    this.doLoad()
  }
}
</script>
