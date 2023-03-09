import { defineStore } from 'pinia'
import { server } from 'src/boot/axios'

export const useRouteStore = defineStore('route', {
  state: () => ({
    from: null,
    to: null,
    range: null,
    savedRoutes: null
  }),
  getters: {
    getFrom: (state) => state.from,
    getTo: (state) => state.to,
    getRange: (state) => state.range,
    getSavedRoutes: (state) => state.savedRoutes
  },
  actions: {
    async showAllSavedRoutesByUserId () {
      try {
        const res = await server.get('/api/user/routes')
        this.savedRoutes = res.data.routesByUserId
        return true
      } catch (error) {
        if (error) throw error
      }
    },
    setRoute (payload) {
      this.from = payload.from
      this.to = payload.to
      this.range = payload.range
    },

    clearRoute () {
      this.from = null
      this.to = null
      this.range = null
      this.savedRoutes = null
    }
  },
  persist: true
})
