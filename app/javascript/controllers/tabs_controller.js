import { Controller } from "@hotwired/stimulus"

// Client-side tab switcher (profile: Posts / Likes, etc.).
// <div data-controller="tabs" data-tabs-active-class="...">
//   <a data-tabs-target="tab" data-action="tabs#select" data-index="0">Posts</a>
//   <div data-tabs-target="panel" data-index="0">…</div>
// </div>
export default class extends Controller {
  static targets = ["tab", "panel"]
  static values = { index: { type: Number, default: 0 } }
  static classes = ["active", "inactive"]

  connect() {
    this.showTab()
  }

  select(event) {
    event.preventDefault()
    this.indexValue = Number(event.currentTarget.dataset.index)
  }

  indexValueChanged() {
    this.showTab()
  }

  showTab() {
    this.tabTargets.forEach((tab) => {
      const active = Number(tab.dataset.index) === this.indexValue
      this.activeClasses.forEach((c) => tab.classList.toggle(c, active))
      this.inactiveClasses.forEach((c) => tab.classList.toggle(c, !active))
      tab.setAttribute("aria-selected", active)
    })
    this.panelTargets.forEach((panel) => {
      panel.classList.toggle("hidden", Number(panel.dataset.index) !== this.indexValue)
    })
  }
}
