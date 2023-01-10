<template>
    <div class="board-page">
        <loading v-if="!board"></loading>
        <nav class="board-menu" v-if="board">
            <button v-on:click="showModal('column-creator')">New Column</button>
            <button v-on:click="showModal('card-creation')">New Card</button>
            <button v-on:click="dumpDB()">DB Dump</button>
        </nav>
        <div class="board" v-if="board">
            <div class="board--column" v-for="column in board.columns" :key="column.id">
                <div class="board--column--header">
                    <div class="board--column--header-title">
                        {{column.title}}
                    </div>
                    <div class="board--column--header-actions">
                        <button class="btn" v-on:click="deleteColumn(column.id)">x</button>
                    </div>
                </div>
                <draggable v-model="column.cards" group="tasks" @start="drag=true" @end="drag=false" class="draggable">
                    <div class="board--column--card" v-for="card in column.cards" :key="card.id">
                        <div class="board--column--card--title">
                            <input class="board--column--card--title--input" v-model="card.title">
                            <button v-on:click="showCard(card)" class="board--column--card--title--btn">Edit</button>
                        </div>
                    </div>
                </draggable>
            </div>
        </div>
        <modal name="card-info" v-if="selectedCard">
            <div class="board-page--card-title">
                <input class="board-page--card-title--input" v-model="selectedCard.title">
            </div>
            <div class="board-page--card-description">
                <textarea v-on:focusout="submitSelectedCardUpdate()" v-model="selectedCard.description" class="board-page--card-description--textarea"></textarea>
            </div>
        </modal>
        <modal name="column-creator">
            <div class="board-page--card-title">
                Creating New Column
            </div>
            <div class="board-page--card-description">
                <div>
                    Column Title:
                </div>
                <input class="form-control mt-1" v-model="forms.newColumn">
                <button class="btn float-end mt-1" v-on:click="submitNewColumn">
                    Submit
                </button>
            </div>
        </modal>
        <modal name="card-creation" v-if="board && board.columns">
            <div class="board-page--card-title">
                <input class="board-page--card-title--input"
                       v-model="forms.newCard.title"
                       v-on:change="errors.cardCreationErrors.titleError = null"
                       :style="errors.cardCreationErrors.titleError"
                       placeholder="Your card title here">
            </div>
            <div class="board-page--card-description">
                <select v-model="forms.newCard.column_id" v-on:change="errors.cardCreationErrors.selectError = null" class="form-control" :style="errors.cardCreationErrors.selectError">
                    <option value="-1">Pick a column</option>
                    <option v-for="column in board.columns" :key="column.id" :value="column.id">{{column.title}}</option>
                </select>
                <textarea
                    v-on:change="errors.cardCreationErrors.descriptionError = null"
                    :style="errors.cardCreationErrors.descriptionError"
                    placeholder="Your card description here"
                    v-model="forms.newCard.description"
                    class="board-page--card-description--textarea board-page--card-description--textarea-new"></textarea>

                <button class="btn" v-on:click="submitNewCard()">Create</button>
            </div>
        </modal>
    </div>
</template>

<script>
import Loading from "./Loading.vue";
import draggable from 'vuedraggable'
export default {
    name: "Welcome",
    components: {Loading, draggable},
    data(){
        return {
            board: null,
            loading: false,
            selectedCard: {
                title: null,
                description: null
            },
            forms:{
                newColumn: null,
                newCard: {
                    title: null,
                    description: null,
                    column_id: -1
                }
            },
            errors: {
                cardCreationErrors: {
                    selectError: null,
                    titleError: null,
                    descriptionError: null
                },
                columnCreationError: null
            }
        }
    },
    methods: {
        async getBoard() {
            let res = await axios.get('/api/board/' + this.$route.params.id)
            this.board = res.data;
        },
        setTokenToAxios(){
            if(!this.$cookies.get('access_token')){
                window.location.href = '/'
                return;
            }
            axios.defaults.headers.common['Authorization'] = `Bearer ${this.$cookies.get('access_token')}`;
        },
        async submitUpdate(newValue){
            this.loading = true
            let res = await axios.post('/api/board/'+this.$route.params.id+'/update-cards', newValue)
            this.loading = false
        },
        async submitSelectedCardUpdate(){
            let res = await axios.post('/api/board/'+this.$route.params.id+'/card/update', this.selectedCard)
            this.loading = false
        },
        showCard (card) {
            this.selectedCard = card
            this.$modal.show('card-info');
        },
        hideCard () {
            this.$modal.hide('card-info');
        },
        showModal(modal){
            this.$modal.show(modal)
        },
        hideModal(modal){
            this.$modal.hide(modal)
        },
        async submitNewColumn(){
            if(!this.forms.newColumn || this.forms.newColumn.length <= 0){
                this.errors.columnCreationError = {
                    border: '1px solid red'
                };
                return;
            }
            let res = await axios.post('/api/board/'+this.$route.params.id+'/column/new', {
                title: this.forms.newColumn
            })
            if(res.data === 'success'){
                await this.getBoard()
            }
            this.hideModal('card-info')
        },
        async deleteColumn(id){
            let res = await axios.delete('/api/board/'+this.$route.params.id+'/column/'+id)
            if(res.data === 'success'){
                await this.getBoard()
            }
        },
        async dumpDB(){
            let res = await axios.get('/api/db-dumper')
            console.log(res.data)
            this.downloadWithAxios(res.data, 'db-dump.sql')
        },
        async submitNewCard(){
            if(!this.forms.newCard.title || this.forms.newCard.title.length <= 0){
                this.errors.cardCreationErrors.titleError = {
                    border: '1px solid red'
                };
                return;
            }
            if(!this.forms.newCard.description || this.forms.newCard.description.length <= 0){
                this.errors.cardCreationErrors.descriptionError = {
                    border: '1px solid red'
                };
                return;
            }
            if(this.forms.newCard.column_id <= 0){
                this.errors.cardCreationErrors.selectError = {
                    border: '1px solid red'
                };
                return;
            }
            let res = await axios.post('/api/board/'+this.$route.params.id+'/card/new', this.forms.newCard)
            if(res.data === 'success'){
                await this.getBoard()
            }
            this.hideModal('card-info')
        },
        forceFileDownload(response, title) {
            const url = window.URL.createObjectURL(new Blob([response.data]))
            const link = document.createElement('a')
            link.href = url
            link.setAttribute('download', title)
            document.body.appendChild(link)
            link.click()
        },
        downloadWithAxios(url, title) {
            axios({
                method: 'get',
                url,
                responseType: 'arraybuffer',
            })
            .then((response) => {
                this.forceFileDownload(response, title)
            })
            .catch(() => console.log('error occured'))
        },
    },
    watch: {
        board: {
            handler(newValue) {
                this.submitUpdate(newValue)
            },
            deep: true
        },
    },
    mounted() {
        this.setTokenToAxios()
        this.getBoard()
    }
}
</script>
