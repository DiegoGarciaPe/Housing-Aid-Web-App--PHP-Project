function animacionLogin() {
    const signInBtnLink = document.querySelector('.signInBtn-link');
    const signUpBtnLink = document.querySelector('.signUpBtn-link');
    const wrapper = document.querySelector('.wrapper');

    signUpBtnLink.addEventListener('click', () => {
        wrapper.classList.toggle('active');
        wrapper.classList.remove('reverse')
    });

    signInBtnLink.addEventListener('click', () => {
        wrapper.classList.toggle('active');
        wrapper.classList.toggle('reverse');
    });
}

document.addEventListener("DOMContentLoaded", () =>{
    animacionLogin();
});


addEventListener('DOMContentLoaded', () => {
    const contadores = document.querySelectorAll('.contador_cantidad');
    const velocidad = 1000
  
    const animarContadores = () => {
      for (const contador of contadores) {
        const actualizar_contador = () => {
          let cantidad_maxima = +contador.dataset.cantidadTotal,
            valor_actual = +contador.innerText,
            incremento = cantidad_maxima / velocidad
  
          if (valor_actual < cantidad_maxima) {
            contador.innerText = Math.ceil(valor_actual + incremento)
            setTimeout(actualizar_contador, 5)
          } else {
            contador.innerText = cantidad_maxima
          }
        }
        actualizar_contador()
      }
    }
  
    const mostrarContadores = elementos => {
      elementos.forEach(elemento => {
        if (elemento.isIntersecting) {
          elemento.target.classList.add('animar')
          elemento.target.classList.remove('ocultar')
          setTimeout(animarContadores, 300)
        }
      });
    }
  
    const observer = new IntersectionObserver(mostrarContadores, {
      threshold: 0.75,
    })
  
    const elementosHTML = document.querySelectorAll('.contador')
    elementosHTML.forEach(elementoHTML =>{
      observer.observe(elementoHTML)
    })
  
  })

//Script para encoger el menu del dashboard

const aside = document.getElementById('aside'),
    menu = document.getElementById('menu');

menu.onclick = () => {
    aside.classList.toggle('active')
}

document.addEventListener("DOMContentLoaded", function() {
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");
    const passwordIcon = togglePassword.querySelector("i");

    togglePassword.addEventListener("click", function() {
        if (password.type === "password") {
            password.type = "text";
            passwordIcon.classList.remove("fa-eye");
            passwordIcon.classList.add("fa-eye-slash");
        } else {
            password.type = "password";
            passwordIcon.classList.remove("fa-eye-slash");
            passwordIcon.classList.add("fa-eye");
        }
    });

    const toggleRegisterPassword = document.getElementById("toggleRegisterPassword");
    const registerPassword = document.getElementById("register-password");
    const registerPasswordIcon = toggleRegisterPassword.querySelector("i");

    toggleRegisterPassword.addEventListener("click", function() {
        if (registerPassword.type === "password") {
            registerPassword.type = "text";
            registerPasswordIcon.classList.remove("fa-eye");
            registerPasswordIcon.classList.add("fa-eye-slash");
        } else {
            registerPassword.type = "password";
            registerPasswordIcon.classList.remove("fa-eye-slash");
            registerPasswordIcon.classList.add("fa-eye");
        }
    });

    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
    const confirmPassword = document.getElementById("confirm-password");
    const confirmPasswordIcon = toggleConfirmPassword.querySelector("i");

    toggleConfirmPassword.addEventListener("click", function() {
        if (confirmPassword.type === "password") {
            confirmPassword.type = "text";
            confirmPasswordIcon.classList.remove("fa-eye");
            confirmPasswordIcon.classList.add("fa-eye-slash");
        } else {
            confirmPassword.type = "password";
            confirmPasswordIcon.classList.remove("fa-eye-slash");
            confirmPasswordIcon.classList.add("fa-eye");
        }
    });
});


document.addEventListener('DOMContentLoaded', (event) => {
    const today = new Date();
    const dd = String(today.getDate()).padStart(2, '0');
    const mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
    const yyyy = today.getFullYear();

    const minDate = yyyy + '-' + mm + '-' + dd;
    const maxDate = (yyyy - 100) + '-' + mm + '-' + dd;

    const fechaNacimientoInput = document.getElementById('fechaNacimiento');
    fechaNacimientoInput.setAttribute('max', minDate);
    fechaNacimientoInput.setAttribute('min', maxDate);

    const paradoCheckbox = document.getElementById('parado');
    const desempleoOptions = document.getElementById('desempleoOptions');

    paradoCheckbox.addEventListener('change', function() {
        if (this.checked) {
            desempleoOptions.style.display = 'block';
        } else {
            desempleoOptions.style.display = 'none';
            const radios = desempleoOptions.querySelectorAll('input[type="radio"]');
            radios.forEach(radio => radio.checked = false);
        }
    });

    // Inicializar el estado de los campos según el valor actual del checkbox "Parado"
    if (paradoCheckbox.checked) {
        desempleoOptions.style.display = 'block';
    } else {
        desempleoOptions.style.display = 'none';
    }

    const paroOptions = desempleoOptions.querySelectorAll('input[name^="Paro"], input[name="Prestacion_Desempleo"], input[name="Derecho_Paro"]');
    const subsidioOptions = desempleoOptions.querySelectorAll('input[name^="Subsidio"], input[name="Derecho_Subsidio"]');

    paroOptions.forEach(option => {
        option.addEventListener('change', function() {
            if (this.value == '1' && this.checked) {
                subsidioOptions.forEach(subOption => {
                    if (subOption.value == '1') subOption.checked = false;
                    if (subOption.value == '0') subOption.checked = true;
                });
            }
        });
    });

    subsidioOptions.forEach(option => {
        option.addEventListener('change', function() {
            if (this.value == '1' && this.checked) {
                paroOptions.forEach(parOption => {
                    if (parOption.value == '1') parOption.checked = false;
                    if (parOption.value == '0') parOption.checked = true;
                });
            }
        });
    });

    document.addEventListener('DOMContentLoaded', (event) => {
        const originalEmail = document.getElementById('originalEmail').value;
        const emailInput = document.getElementById('email');
        const profileForm = document.getElementById('profileForm');
    
        profileForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Evitar el envío del formulario por defecto
    
            if (emailInput.value !== originalEmail) {
                Swal.fire({
                    title: 'Confirmar cambio de email',
                    text: 'Has cambiado tu email. Se cerrará la sesión para actualizar tu información. ¿Deseas continuar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, actualizar',
                    cancelButtonText: 'No, revertir cambios'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Enviar el formulario
                        var formData = new FormData(profileForm);
    
                        fetch('actualizarPerfil.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log(data);
                            if (data.includes("Registro actualizado correctamente")) {
                                Swal.fire(
                                    'Actualizado',
                                    'Tu perfil ha sido actualizado correctamente.',
                                    'success'
                                ).then(() => {
                                    window.location.href = 'logout.php'; // Cerrar sesión
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    'Hubo un problema al actualizar tu perfil.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire(
                                'Error',
                                'Hubo un problema al actualizar tu perfil.',
                                'error'
                            );
                        });
                    } else {
                        // Revertir cambios de email
                        emailInput.value = originalEmail;
                    }
                });
            } else {
                // Enviar el formulario directamente si no hay cambios en el email
                var formData = new FormData(profileForm);
    
                fetch('actualizarPerfil.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    Swal.fire(
                        'Actualizado',
                        'Tu perfil ha sido actualizado correctamente.',
                        'success'
                    );
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire(
                        'Error',
                        'Hubo un problema al actualizar tu perfil.',
                        'error'
                    );
                });
            }
        });
    });
        
});
    document.addEventListener('DOMContentLoaded', (event) => {
        const originalEmail = document.getElementById('originalEmail').value;
        const emailInput = document.getElementById('email');
        const profileForm = document.getElementById('profileForm');

        profileForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Evitar el envío del formulario por defecto

            if (emailInput.value !== originalEmail) {
                Swal.fire({
                    title: 'Confirmar cambio de email',
                    text: 'Has cambiado tu email. Se cerrará la sesión para actualizar tu información. ¿Deseas continuar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, actualizar',
                    cancelButtonText: 'No, revertir cambios'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Enviar el formulario
                        var formData = new FormData(profileForm);

                        fetch('actualizarPerfil.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log(data);
                            if (data.includes("Registro actualizado correctamente")) {
                                Swal.fire(
                                    'Actualizado',
                                    'Tu perfil ha sido actualizado correctamente.',
                                    'success'
                                ).then(() => {
                                    window.location.href = 'logout.php'; // Cerrar sesión
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    'Hubo un problema al actualizar tu perfil.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire(
                                'Error',
                                'Hubo un problema al actualizar tu perfil.',
                                'error'
                            );
                        });
                    } else {
                        // Revertir cambios de email
                        emailInput.value = originalEmail;
                    }
                });
            } else {
                // Enviar el formulario directamente si no hay cambios en el email
                var formData = new FormData(profileForm);

                fetch('actualizarPerfil.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    Swal.fire(
                        'Actualizado',
                        'Tu perfil ha sido actualizado correctamente.',
                        'success'
                    );
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire(
                        'Error',
                        'Hubo un problema al actualizar tu perfil.',
                        'error'
                    );
                });
            }
        });
    });



document.addEventListener('DOMContentLoaded', function () {
    const searchQueryInput = document.getElementById('searchQuery');
    const consultasContainer = document.getElementById('consultasContainer');

    // Función para cargar las consultas
    function loadConsultas(query = '') {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'searchConsultas.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (this.status === 200) {
                consultasContainer.innerHTML = this.responseText;
                addCheckboxEventListeners();
            }
        };
        xhr.send('query=' + query);
    }

    // Función para añadir event listeners a los checkboxes
    function addCheckboxEventListeners() {
        const checkboxes = document.querySelectorAll('.resueltaCheckbox');
        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const id = this.getAttribute('data-id');
                const resuelta = this.checked ? 1 : 0;
                updateStatus(id, resuelta);
            });
        });
    }

    // Función para actualizar el estado de las consultas
    function updateStatus(id, resuelta) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'updateStatus.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (this.status !== 200) {
                alert('Error al actualizar el estado de la consulta.');
            }
        };
        xhr.send('id=' + id + '&resuelta=' + resuelta);
    }

    // Event listener para el campo de búsqueda
    searchQueryInput.addEventListener('input', function () {
        loadConsultas(this.value);
    });

    // Cargar todas las consultas al cargar la página
    loadConsultas();
});
