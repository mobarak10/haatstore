<template>
    <div class="pad flex-fill salesman">
        <button type="button"
                data-toggle="modal"
                data-target=".salesman-modal">
            {{ salesman.name || 'Select Salesman'}}
        </button>


        <!-- Modal -->
        <div class="modal fade salesman-modal" role="dialog" ref="modal">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Select Salesman</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group mb-2">
                                <label for="salesman-name" class="sr-only">Salesman Name</label>
                                <input type="text" v-model="searchSalesman" class="form-control form-control-sm" id="salesman-name" placeholder="Salesman Name">
                            </div>
                        </form>
                        <div class="list-group">
                            <a href="#" v-for="(_salesman, index) in filteredSalesmen" :key="index"
                               :class="['list-group-item', 'list-group-item-action', (salesmanId === _salesman.id) ? 'list-group-item-info' : '']"
                               @click.prevent="selectSalesman(_salesman.id)"
                            >
                                <span class="row">
                                    <span class="col-md-3">
                                        <img :src="baseURL + ((_salesman.media) ? _salesman.media.real_path : 'public/images/avatars/user.png')" class="img-fluid" :alt="_salesman.name">
                                    </span>
                                    <span class="col-md-9">
                                        <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">{{ _salesman.name }}</h5>
                                </div>
                                <p class="mb-1">{{ _salesman.phone }}</p>
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "SalesmanComponent",
        computed: {
            filteredSalesmen(){
                if (this.searchSalesman){
                    return this.salesmen.filter(_salesman => {
                        return _salesman.name.toLowerCase().includes(this.searchSalesman.toLowerCase())
                    })
                }else{
                    return this.salesmen
                }
            },
            salesman(){
                if (this.salesmanId){
                    return this.salesmen.find(_salesman => _salesman.id === this.salesmanId)
                }else{
                    // return this.salesmen.find(_salesman => _salesman.id === this.salesmanId)
                    return {}
                }
            }
        },
        data(){
            return {
                salesmen:[],
                salesmanId: null,
                searchSalesman: null,
                loginSalesMan: null
            }
        },
        methods: {
            getSalesmen(){
                axios.post(baseURL + 'get-salesmen')
                .then(response => {
                    console.log(response.data)
                    this.salesmen = response.data.salesmen
                    this.salesmanId = response.data.login_user_id
                })
                .catch(reason => console.log(reason))
            },
            selectSalesman(id){
                this.salesmanId = id
            }
        },
        mounted() {
            this.getSalesmen()
        }
    }
</script>

<style scoped>
.salesman > button {
    background: #b423c5 !important;
}
</style>
