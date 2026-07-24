import { Controller } from "@hotwired/stimulus"

// Show/hide password field.
export default class extends Controller {
  static targets = ["field", "show", "hide"]

  toggle() {
    const hidden = this.fieldTarget.type === "password"
    this.fieldTarget.type = hidden ? "text" : "password"
    this.showTarget?.classList.toggle("hidden", hidden)
    this.hideTarget?.classList.toggle("hidden", !hidden)
  }
}
