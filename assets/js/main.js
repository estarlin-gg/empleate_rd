

import * as bootstrap from "bootstrap"

document.addEventListener("DOMContentLoaded", () => {

  let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  let tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  let popoverList = popoverTriggerList.map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl))

  // Form validation
  let forms = document.querySelectorAll(".needs-validation")
  Array.prototype.slice.call(forms).forEach((form) => {
    form.addEventListener(
      "submit",
      (event) => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add("was-validated")
      },
      false,
    )
  })

  
  let passwordInput = document.getElementById("password")
  let confirmPasswordInput = document.getElementById("confirm_password")

  if (passwordInput && confirmPasswordInput) {
    confirmPasswordInput.addEventListener("input", () => {
      if (passwordInput.value !== confirmPasswordInput.value) {
        confirmPasswordInput.setCustomValidity("Las contraseñas no coinciden")
      } else {
        confirmPasswordInput.setCustomValidity("")
      }
    })

    passwordInput.addEventListener("input", () => {
      if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
        confirmPasswordInput.setCustomValidity("Las contraseñas no coinciden")
      } else {
        confirmPasswordInput.setCustomValidity("")
      }
    })
  }

  
  let photoInput = document.getElementById("photo")
  let photoPreview = document.getElementById("photo-preview")

  if (photoInput && photoPreview) {
    photoInput.addEventListener("change", function () {
      if (this.files && this.files[0]) {
        let reader = new FileReader()

        reader.onload = (e) => {
          photoPreview.src = e.target.result
          photoPreview.style.display = "block"
        }

        reader.readAsDataURL(this.files[0])
      }
    })
  }
})
