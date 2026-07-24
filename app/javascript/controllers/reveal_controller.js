import { Controller } from "@hotwired/stimulus"

// Generic show/hide toggle (e.g. inline comment box, "read more").
// <div data-controller="reveal">
//   <button data-action="reveal#toggle">Reply</button>
//   <div data-reveal-target="item" class="hidden">…</div>
// </div>
export default class extends Controller {
  static targets = ["item"]
  static values = { focus: Boolean }

  toggle(event) {
    event?.preventDefault()
    this.itemTargets.forEach((el) => el.classList.toggle("hidden"))
    if (this.focusValue) {
      const field = this.itemTargets[0]?.querySelector("input, textarea")
      field?.focus()
    }
  }

  hide() {
    this.itemTargets.forEach((el) => el.classList.add("hidden"))
  }
}
