import { Controller } from "@hotwired/stimulus"

// Live character counter with a soft/hard limit and colour feedback.
// <div data-controller="char-counter" data-char-counter-max-value="1000">
//   <textarea data-char-counter-target="input" data-action="input->char-counter#update"></textarea>
//   <span data-char-counter-target="output"></span>
// </div>
export default class extends Controller {
  static targets = ["input", "output", "submit"]
  static values = { max: Number }

  connect() {
    this.update()
  }

  update() {
    const length = this.inputTarget.value.length
    const remaining = this.maxValue - length
    this.outputTarget.textContent = remaining

    const warn = remaining < this.maxValue * 0.1
    const over = remaining < 0
    this.outputTarget.classList.toggle("text-amber-500", warn && !over)
    this.outputTarget.classList.toggle("text-red-500", over)
    this.outputTarget.classList.toggle("text-gray-400", !warn && !over)

    if (this.hasSubmitTarget) this.submitTarget.disabled = over || length === 0
  }
}
