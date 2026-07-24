import { Controller } from "@hotwired/stimulus"

// Optimistic like: flips the heart + count instantly, then lets the Turbo
// Stream response from the server replace the button with the source of truth.
export default class extends Controller {
  static targets = ["icon", "count"]
  static values = { liked: Boolean }

  toggle() {
    const liked = !this.likedValue
    this.likedValue = liked

    // Heart fill + pop animation
    this.iconTarget.classList.toggle("fill-current", liked)
    this.iconTarget.classList.toggle("text-red-500", liked)
    this.iconTarget.classList.add("scale-125")
    setTimeout(() => this.iconTarget.classList.remove("scale-125"), 150)

    // Bump the count optimistically
    if (this.hasCountTarget) {
      const current = parseInt(this.countTarget.textContent, 10) || 0
      this.countTarget.textContent = current + (liked ? 1 : -1)
    }
  }
}
