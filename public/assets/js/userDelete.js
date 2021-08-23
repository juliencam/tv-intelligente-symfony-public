const userDelete = { 

    init: function() {

        //console.log('userDelete');

        let form = document.querySelector('.js-form-user-remove')
        form.addEventListener("submit", userDelete.handleDelete)
    },

    handleDelete: function(event) {


        let result = confirm('Êtes-vous sûr de vouloir supprimer votre compte?')

        if (result === false) {
            event.preventDefault()
        }

    }
}

document.addEventListener('DOMContentLoaded', userDelete.init);