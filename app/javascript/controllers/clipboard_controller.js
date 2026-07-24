import { Controller } from "@hotwired/stimulus"

// Copy text to the clipboard and flash a confirmation.
// <button data-controller="clipboard" data-clipboard-text-value="https://…"
//         data-action="clipboard#copy">
//   <span data-clipboard-target="label">Copy link</span>
// </button>
export default class extends Controller {
  static values = { text: String, successText: { type: String, default: "Copied!" } }
  static targets = ["label"]

  async copy(event) {
    event.preventDefault()
    const text = this.textValue || window.location.href
    try {
      await navigator.clipboard.writeText(text)
      this.flash()
    } catch (_e) {
      // Fallback for insecure contexts
      const input = document.createElement("input")
      input.value = text
      document.body.appendChild(input)
      input.select()
      document.execCommand("copy")
      input.remove()
      this.flash()
    }
  }

  flash() {
    if (!this.hasLabelTarget) return
    const original = this.labelTarget.textContent
    this.labelTarget.textContent = this.successTextValue
    setTimeout(() => (this.labelTarget.textContent = original), 1500)
  }
}
