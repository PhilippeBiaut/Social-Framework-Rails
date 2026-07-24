import { Controller } from "@hotwired/stimulus"

// Reusable modal with backdrop, ESC to close, focus management and body scroll lock.
// <div data-controller="modal">
//   <button data-action="modal#open">New post</button>
//   <div data-modal-target="dialog" class="hidden">…<button data-action="modal#close">×</button></div>
// </div>
export default class extends Controller {
  static targets = ["dialog"]
  static values = { open: Boolean }

  connect() {
    this.onKeydown = this.onKeydown.bind(this)
    window.addEventListener("keydown", this.onKeydown)
  }

  disconnect() {
    window.removeEventListener("keydown", this.onKeydown)
  }

  onKeydown(event) {
    if (event.key === "Escape" && this.openValue) this.close(event)
  }

  open(event) {
    event?.preventDefault()
    this.dialogTarget.classList.remove("hidden")
    this.dialogTarget.classList.add("flex")
    document.body.classList.add("overflow-hidden")
    this.openValue = true
    const field = this.dialogTarget.querySelector("[autofocus], textarea, input")
    field?.focus()
  }

  close(event) {
    event?.preventDefault()
    this.dialogTarget.classList.add("hidden")
    this.dialogTarget.classList.remove("flex")
    document.body.classList.remove("overflow-hidden")
    this.openValue = false
  }

  // Close when clicking the backdrop itself (not the panel).
  backdrop(event) {
    if (event.target === this.dialogTarget) this.close(event)
  }
}
