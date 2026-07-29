import { Controller } from "@hotwired/stimulus"

// Reusable modal: backdrop click, Esc to close, body scroll lock, and the focus
// handling a dialog owes a keyboard user — move focus in, trap Tab inside, and
// hand focus back to whatever opened it.
// <div data-controller="modal">
//   <button data-action="modal#open">Open</button>
//   <div data-modal-target="dialog" role="dialog" aria-modal="true" class="hidden">
//     …<button data-action="modal#close">×</button>
//   </div>
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
    document.body.classList.remove("overflow-hidden")
  }

  onKeydown(event) {
    if (!this.openValue) return

    if (event.key === "Escape") {
      this.close(event)
    } else if (event.key === "Tab") {
      this.trap(event)
    }
  }

  open(event) {
    event?.preventDefault()
    this.opener = document.activeElement

    this.dialogTarget.classList.remove("hidden")
    this.dialogTarget.classList.add("flex")
    document.body.classList.add("overflow-hidden")
    this.openValue = true

    const target = this.dialogTarget.querySelector("[autofocus], textarea, input") || this.focusables()[0]
    target?.focus()
  }

  close(event) {
    event?.preventDefault()
    this.dialogTarget.classList.add("hidden")
    this.dialogTarget.classList.remove("flex")
    document.body.classList.remove("overflow-hidden")
    this.openValue = false

    // Return the caret to where the user left it.
    this.opener?.focus()
  }

  // Close when clicking the backdrop itself (not the panel).
  backdrop(event) {
    if (event.target === this.dialogTarget) this.close(event)
  }

  // Keep Tab inside the dialog while it is open.
  trap(event) {
    const items = this.focusables()
    if (items.length === 0) return

    const first = items[0]
    const last = items[items.length - 1]

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault()
      last.focus()
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault()
      first.focus()
    } else if (!this.dialogTarget.contains(document.activeElement)) {
      event.preventDefault()
      first.focus()
    }
  }

  focusables() {
    return Array.from(
      this.dialogTarget.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
      )
    ).filter((el) => el.offsetWidth > 0 || el.offsetHeight > 0 || el === document.activeElement)
  }
}
