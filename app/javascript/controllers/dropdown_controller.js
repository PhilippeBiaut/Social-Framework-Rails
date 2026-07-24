import { Controller } from "@hotwired/stimulus"

// Accessible dropdown menu (user menu, post "…" menu).
// Usage:
//   <div data-controller="dropdown" data-action="click@window->dropdown#hide keydown.esc@window->dropdown#hide">
//     <button data-action="dropdown#toggle" data-dropdown-target="button">…</button>
//     <div data-dropdown-target="menu" class="hidden">…</div>
//   </div>
export default class extends Controller {
  static targets = ["menu", "button"]

  toggle(event) {
    event.stopPropagation()
    this.menuTarget.classList.toggle("hidden")
    this.setExpanded(!this.menuTarget.classList.contains("hidden"))
  }

  hide(event) {
    if (event && this.element.contains(event.target)) return
    this.menuTarget.classList.add("hidden")
    this.setExpanded(false)
  }

  setExpanded(value) {
    if (this.hasButtonTarget) this.buttonTarget.setAttribute("aria-expanded", value)
  }
}
