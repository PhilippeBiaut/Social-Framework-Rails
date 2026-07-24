import { Controller } from "@hotwired/stimulus"

// Auto-growing textarea.
export default class extends Controller {
  connect() {
    this.resize()
  }

  resize() {
    this.element.style.height = "auto"
    this.element.style.height = `${this.element.scrollHeight}px`
  }
}
