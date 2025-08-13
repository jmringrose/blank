import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('api_token') || null,
    isAuthenticated: false
  }),

  getters: {
    isLoggedIn: (state) => !!state.token && !!state.user
  },

  actions: {
    async login(credentials) {
      try {
        // Login request - no CSRF needed for API tokens
        const response = await axios.post('/login', credentials)
        
        this.token = response.data.token
        this.isAuthenticated = true
        
        // Set axios default header
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        // Store token in localStorage
        localStorage.setItem('api_token', this.token)
        
        // Fetch user data
        await this.fetchUser()
        
        return response.data
      } catch (error) {
        this.logout()
        throw error
      }
    },

    async logout() {
      try {
        if (this.token) {
          await axios.post('/logout')
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        this.isAuthenticated = false
        delete axios.defaults.headers.common['Authorization']
        localStorage.removeItem('api_token')
      }
    },

    async fetchUser() {
      if (!this.token) return null
      
      try {
        const response = await axios.get('/user')
        this.user = response.data
        this.isAuthenticated = true
        return response.data
      } catch (error) {
        console.error('Fetch user error:', error)
        this.logout()
        return null
      }
    },

    setToken(token) {
      this.token = token
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        localStorage.setItem('api_token', token)
      }
    },

    async initializeAuth() {
      const token = localStorage.getItem('api_token')
      if (token) {
        this.setToken(token)
        await this.fetchUser()
      }
    }
  }
})