import { Controller } from "@hotwired/stimulus"

// Visual password strength meter (length + character variety heuristic).
export default class extends Controller {
  static targets = ["input", "bar", "label"]

  static LEVELS = [
    { label: "Too weak", color: "bg-red-500", width: "20%" },
    { label: "Weak", color: "bg-orange-500", width: "40%" },
    { label: "Fair", color: "bg-yellow-500", width: "60%" },
    { label: "Good", color: "bg-lime-500", width: "80%" },
    { label: "Strong", color: "bg-green-500", width: "100%" }
  ]

  update() {
    const value = this.inputTarget.value
    const score = this.score(value)
    const level = this.constructor.LEVELS[Math.max(0, score - 1)]

    this.barTarget.className = `h-1.5 rounded-full transition-all duration-300 ${value ? level.color : "bg-gray-200 dark:bg-gray-600"}`
    this.barTarget.style.width = value ? level.width : "0%"
    this.labelTarget.textContent = value ? level.label : ""
  }

  score(value) {
    if (!value) return 0
    let score = 0
    if (value.length >= 8) score++
    if (value.length >= 12) score++
    if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++
    if (/\d/.test(value)) score++
    if (/[^A-Za-z0-9]/.test(value)) score++
    return Math.min(score, 5)
  }
}
