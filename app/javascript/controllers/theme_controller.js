import { Controller } from "@hotwired/stimulus"

// Toggles class-based dark mode and persists the choice.
// Connected on <html> via data-controller="theme".
export default class extends Controller {
  static targets = ["sun", "moon"]

  connect() {
    this.apply(this.preferred())
  }

  toggle() {
    const next = this.isDark() ? "light" : "dark"
    localStorage.setItem("theme", next)
    this.apply(next)
  }

  apply(theme) {
    const dark = theme === "dark"
    document.documentElement.classList.toggle("dark", dark)
    this.sunTargets.forEach((el) => el.classList.toggle("hidden", dark))
    this.moonTargets.forEach((el) => el.classList.toggle("hidden", !dark))
  }

  isDark() {
    return document.documentElement.classList.contains("dark")
  }

  preferred() {
    const saved = localStorage.getItem("theme")
    if (saved) return saved
    return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"
  }
}
