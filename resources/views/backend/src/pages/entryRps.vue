<template>
<div>
  <b-row>
    <b-colxx xxs="12">
      <piaf-breadcrumb :heading="'Entry RPS'" />
      <div class="separator mb-5"></div>
    </b-colxx>
  </b-row>
  <b-row>
    <b-colxx xxs="12">
      <b-card class="mb-4">
        <b-row class="d-flex align-items-end mb-3">
          <b-col xs=12 sm=3 md=2>
            <b-form-group class="mb-0" label="Semester">
              <b-select />
            </b-form-group>
          </b-col>
          <b-col xs=12 sm=3 md=2>
            <b-form-group class="mb-0" label="Mata Kuliah + SKS">
              <b-select />
            </b-form-group>
          </b-col>
          <b-col xs=12 sm=3 md=2>
            <b-button variant="primary" squared>Submit</b-button>
          </b-col>
        </b-row>
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
          <b-table show-empty small stacked="md" 
            :items="table.data" 
            :fields="table.field" 
            :current-page="paging.currentPage" 
            :sort-by.sync="paging.sortBy" 
            :sort-direction="paging.sortDirection" 
            :sort-desc.sync="paging.sortDesc" 
            bordered striped head-variant="dark" hover per-page="0">
            <template v-slot:cell(no)="row">
              {{ (paging.currentPage-1) * paging.perPage + row.index + 1 }}
            </template>
            <template v-slot:cell(actions)="row">
              <b-button size="xs" variant="danger" squared @click="doDelete(row.item.id)">
                <b-icon icon="trash"></b-icon>
              </b-button>
              <b-button size="xs" variant="info" squared @click="doUpdate(row.item.id)">
                <b-icon icon="pencil"></b-icon>
              </b-button>
              <b-button size="xs" variant="success" squared @click="doAdd(row.item.id)"> 
                <b-icon icon="plus"></b-icon>
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
      </b-card>
    </b-colxx>
  </b-row>
</div>
</template>
<script>
export default {
  name : 'entryRps',
  data() {
    return {
      table : {
        field : [
          {
            key: 'no',
            label: 'No',
            class: 'text-center w-5'
          },
          {
            key: 'textbook',
            label: 'Textbook',
            sortable: true,
            thClass: 'text-center',
            tdClass : ''
          },
          {
            key: 'attribute',
            label: 'Attribute',
            sortable: true,
            thClass: 'text-center',
            tdClass : ''
          },
          {
            key: 'cp',
            label: 'CP',
            sortable: true,
            thClass: 'text-center',
            tdClass : ''
          },
          {
            key: 'summary',
            label: 'Summary',
            sortable: true,
            thClass: 'text-center',
            tdClass : ''
          },
          {
            key: 'action',
            label: 'Action',
            sortable: true,
            thClass: 'text-center',
            tdClass : ''
          },
        ],
        data : [],
        loading : false
      },
      paging : {
        currentPage : 1,
        perPage : 10,
        sortBy : 'id',
        sortDirection : 'desc',
        sortDesc : true,
        totalRows : 0,
        search : ''
      },
      opt : {
        pageOptions : [10,20,50,100,200]
      }
    }
  }
}
</script>
