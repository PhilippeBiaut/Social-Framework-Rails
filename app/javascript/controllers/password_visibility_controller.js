import { Controller } from "@hotwired/stimulus"

// Show/hide password field.
export default class extends Controller {
  static targets = ["field", "show", "hide"]

  toggle() {
    const revealed = this.fieldTarget.type === "password"
    this.fieldTarget.type = revealed ? "text" : "password"
    // Icons are optional; a missing singular target would throw in Stimulus.
    if (this.hasShowTarget) this.showTarget.classList.toggle("hidden", revealed)
    if (this.hasHideTarget) this.hideTarget.classList.toggle("hidden", !revealed)
  }
}
