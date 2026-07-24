import { Controller } from "@hotwired/stimulus"

// Auto-dismissing flash toast with a fade-out transition.
export default class extends Controller {
  static values = { delay: { type: Number, default: 4000 } }

  connect() {
    this.timeout = setTimeout(() => this.dismiss(), this.delayValue)
  }

  disconnect() {
    clearTimeout(this.timeout)
  }

  dismiss() {
    this.element.classList.add("opacity-0", "translate-x-4")
    setTimeout(() => this.element.remove(), 300)
  }
}
