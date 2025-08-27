import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["button", "dropdown", "selectedText", "hiddenInput", "searchInput", "itemsContainer", "arrow", "createForm"]
  static values = { frameId: String }

  connect() {
    // Listen for successful form submissions to close dropdown and update selection
    document.addEventListener('turbo:submit-end', this.handleFormSubmit.bind(this))
  }

  disconnect() {
    document.removeEventListener('turbo:submit-end', this.handleFormSubmit.bind(this))
  }

  toggle() {
    if (this.dropdownTarget.classList.contains('hidden')) {
      this.open()
    } else {
      this.close()
    }
  }

  open() {
    // Close all other dropdowns first
    document.querySelectorAll('[data-controller="turbo-select"] [data-turbo-select-target="dropdown"]').forEach(dropdown => {
      if (dropdown !== this.dropdownTarget) {
        dropdown.classList.add('hidden')
      }
    })
    
    // Close all other dropdown arrows
    document.querySelectorAll('[data-controller="turbo-select"] [data-turbo-select-target="arrow"]').forEach(arrow => {
      arrow.classList.remove('rotate-180')
    })
    
    this.dropdownTarget.classList.remove('hidden')
    
    // Rotate arrow
    if (this.hasArrowTarget) {
      this.arrowTarget.classList.add('rotate-180')
    }
    
    // Focus search input if it exists
    if (this.hasSearchInputTarget) {
      setTimeout(() => this.searchInputTarget.focus(), 100)
    }
  }

  close() {
    this.dropdownTarget.classList.add('hidden')
    
    // Rotate arrow back
    if (this.hasArrowTarget) {
      this.arrowTarget.classList.remove('rotate-180')
    }
    
    // Clear search input
    if (this.hasSearchInputTarget) {
      this.searchInputTarget.value = ''
      this.search()
    }
  }

  selectItem(event) {
    const value = event.currentTarget.dataset.value
    const label = event.currentTarget.dataset.label
    
    this.hiddenInputTarget.value = value
    this.selectedTextTarget.textContent = label
    this.close()
    
    // Highlight selected item
    this.itemsContainerTarget.querySelectorAll('[data-value]').forEach(item => {
      item.classList.remove('bg-blue-50', 'dark:bg-blue-900')
    })
    event.currentTarget.classList.add('bg-blue-50', 'dark:bg-blue-900')
    
    // Dispatch change event
    this.hiddenInputTarget.dispatchEvent(new CustomEvent('change', {
      detail: { value: value, label: label }
    }))
    
    // Dispatch specific events for client changes
    if (this.hiddenInputTarget.name === 'client_id') {
      document.dispatchEvent(new CustomEvent('client-changed', {
        detail: { value: value, label: label }
      }))
      
      // Update project select to filter by this client
      this.updateProjectSelect(value)
    }
  }

  search() {
    if (!this.hasSearchInputTarget) return
    
    const searchTerm = this.searchInputTarget.value.toLowerCase()
    const items = this.itemsContainerTarget.querySelectorAll('[data-value]')
    
    items.forEach(item => {
      const label = item.dataset.label.toLowerCase()
      if (label.includes(searchTerm)) {
        item.style.display = 'block'
      } else {
        item.style.display = 'none'
      }
    })
  }

  updateProjectSelect(clientId) {
    // Find project select on the same form
    const form = this.element.closest('form')
    if (form) {
      const projectSelect = form.querySelector('[name="project_id"]')
      if (projectSelect) {
        const projectController = this.application.getControllerForElementAndIdentifier(
          projectSelect.closest('[data-controller="turbo-select"]'), 
          'turbo-select'
        )
        if (projectController) {
          projectController.filterByClient(clientId)
        }
      }
    }
  }

  filterByClient(clientId) {
    const items = this.itemsContainerTarget.querySelectorAll('[data-value]')
    
    items.forEach(item => {
      const itemClientId = item.dataset.clientId
      
      if (!clientId || itemClientId === clientId) {
        item.style.display = 'block'
      } else {
        item.style.display = 'none'
      }
    })
    
    // Clear selection if current selection doesn't match filter
    const currentValue = this.hiddenInputTarget.value
    if (currentValue) {
      const currentItem = this.itemsContainerTarget.querySelector(`[data-value="${currentValue}"]`)
      if (currentItem && currentItem.style.display === 'none') {
        this.clearSelection()
      }
    }
  }

  clearSelection() {
    this.hiddenInputTarget.value = ''
    this.selectedTextTarget.textContent = this.selectedTextTarget.dataset.placeholder || 'Select...'
    
    // Remove highlight from all items
    this.itemsContainerTarget.querySelectorAll('[data-value]').forEach(item => {
      item.classList.remove('bg-blue-50', 'dark:bg-blue-900')
    })
  }

  handleFormSubmit(event) {
    // Check if this form submission was for creating a new item for this select
    const response = event.detail.fetchResponse
    if (response && response.succeeded) {
      const form = event.target
      const frameId = form.closest('turbo-frame')?.id
      
      // If this was a create form in our frame, close it and refresh the select
      if (frameId && frameId.includes(this.frameIdValue)) {
        setTimeout(() => {
          // Close the create frame
          const createFrame = document.getElementById(frameId)
          if (createFrame) {
            createFrame.innerHTML = ''
          }
          
          // Refresh the dropdown by reloading the original frame content
          this.refreshDropdown()
        }, 100)
      }
    }
  }

  refreshDropdown() {
    // This would typically reload the dropdown content
    // For now, we'll just close it - in a real app you'd want to refresh the items
    this.close()
  }

  handleKeydown(event) {
    if (event.key === 'Enter') {
      event.preventDefault()
      this.handleCreate(event)
    }
  }

  handleCreate(event) {
    event.preventDefault()
    
    const searchValue = this.searchInputTarget.value.trim()
    
    // For clients: Check if it matches the format: name,rate,currency
    if (this.hiddenInputTarget.name === 'client_id') {
      const parts = searchValue.split(',').map(part => part.trim())
      
      if (parts.length === 3) {
        const [name, rate, currency] = parts
        
        // Validate the format
        if (name && !isNaN(parseFloat(rate)) && currency.length === 3) {
          this.createFromSearch(name, rate, currency.toUpperCase())
          return
        }
      }
    }
    
    // For projects: Just name is enough
    if (this.hiddenInputTarget.name === 'project_id' && searchValue) {
      this.createFromSearch(searchValue)
      return
    }
    
    // If not in correct format, do normal search
    this.search()
  }

  createFromSearch(name, rate = null, currency = null) {
    // Create form data
    const formData = new FormData()
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content)
    formData.append('name', name)
    formData.append('quick_create', '1')
    
    // Only add rate and currency for clients
    if (rate && currency) {
      formData.append('hourly_rate', rate)
      formData.append('currency', currency)
    }
    
    // Determine the create URL based on field name
    let createUrl = ''
    if (this.hiddenInputTarget.name === 'client_id') {
      createUrl = '/clients'
    } else if (this.hiddenInputTarget.name === 'project_id') {
      createUrl = '/projects'
    }
    
    // Make the request
    fetch(createUrl, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Add the new item to the select
        this.hiddenInputTarget.value = data.id
        this.selectedTextTarget.textContent = data.name
        this.close()
        
        // Dispatch change event
        this.hiddenInputTarget.dispatchEvent(new CustomEvent('change', {
          detail: { value: data.id, label: data.name }
        }))
        
        // Dispatch specific events for client changes
        if (this.hiddenInputTarget.name === 'client_id') {
          document.dispatchEvent(new CustomEvent('client-changed', {
            detail: { value: data.id, label: data.name }
          }))
        }
        
        // Clear the search input
        this.searchInputTarget.value = ''
      } else {
        // If there's an error, redirect to the full create page
        window.location.href = data.redirect_url
      }
    })
    .catch(error => {
      console.error('Error creating item:', error)
      // On error, redirect to the full create page
      if (this.hiddenInputTarget.name === 'client_id') {
        window.location.href = '/clients/create'
      } else if (this.hiddenInputTarget.name === 'project_id') {
        window.location.href = '/projects/create'
      }
    })
  }
}