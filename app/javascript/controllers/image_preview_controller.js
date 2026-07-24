import { Controller } from "@hotwired/stimulus"

// Previews an image file before upload and offers a "remove" action.
// <div data-controller="image-preview">
//   <input type="file" data-image-preview-target="input" data-action="image-preview#preview">
//   <div data-image-preview-target="wrapper" class="hidden">
//     <img data-image-preview-target="image">
//     <button data-action="image-preview#clear">Remove</button>
//   </div>
// </div>
export default class extends Controller {
  static targets = ["input", "image", "wrapper", "placeholder"]

  preview() {
    const file = this.inputTarget.files[0]
    if (!file) return this.clear()

    const reader = new FileReader()
    reader.onload = (e) => {
      this.imageTarget.src = e.target.result
      this.wrapperTarget.classList.remove("hidden")
      this.placeholderTarget?.classList.add("hidden")
    }
    reader.readAsDataURL(file)
  }

  clear(event) {
    event?.preventDefault()
    this.inputTarget.value = ""
    this.imageTarget.src = ""
    this.wrapperTarget.classList.add("hidden")
    this.placeholderTarget?.classList.remove("hidden")
  }
}
