import { Controller } from "@hotwired/stimulus"

// Debounced form auto-submit (live search).
// <form data-controller="autosubmit">
//   <input data-action="input->autosubmit#submit">
// </form>
export default class extends Controller {
  static values = { delay: { type: Number, default: 300 } }

  submit() {
    clearTimeout(this.timeout)
    this.timeout = setTimeout(() => {
      this.element.requestSubmit()
    }, this.delayValue)
  }

  disconnect() {
    clearTimeout(this.timeout)
  }
}
