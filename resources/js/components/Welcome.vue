<template>
    <div class="welcome-container">
        <div class="welcome-card">
            <div class="welcome-card-body">
                <div>
                    Current user token:
                </div>
                <input :value="token" readonly class="mt-2">
                <div class="board-list" v-if="boards">
                    <a v-for="board in boards" :href="'/board/'+board.id" class="board-list-a">{{board.title}}</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Welcome",
    data(){
        return {
            token: '7696b09a-1522-4931-b57e-46ec37edb95f',
            boards: null
        }
    },
    mounted() {
        this.setupToken()
        this.getBoards()
    },
    methods: {
        setupToken(){
            this.$cookies.set('access_token', this.token)
            axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        },
        async getBoards() {
            let res = await axios.get('/api/boards')
            this.boards = res.data
        }
    }
}
</script>
