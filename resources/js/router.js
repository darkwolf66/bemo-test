import VueRouter from "vue-router";
import Welcome from "./components/Welcome.vue";
import Board from "./components/Board.vue";

const routes = [
    {
        path: '/',
        name: 'Hello',
        component: Welcome
    },
    {
        path: '/board/:id',
        name: 'Board',
        component: Board
    }
]
const router = new VueRouter({
    mode: 'history',
    routes
})

export default router;
