const commentDelete = { 

    init: function() {

        //console.log('commentDelete.js');

        let formDeletes = document.querySelectorAll('.js-comment-form-delete')

        for (let formDelete of formDeletes) {

            formDelete.addEventListener('submit', commentDelete.handleDeleteComment)
        }
    },

    handleDeleteComment: function(event) {

        event.preventDefault()

        //console.log(event);

        let result = confirm('Êtes-vous sûr de vouloir supprimer ce commentaire?')

        if (result === false) {
            document.location.reload();
            return true
        }
        //console.log(result);

        let tokenForm = event.currentTarget.elements[1].value

        let url = event.currentTarget.getAttribute("action")

        let form = event.currentTarget

        axios.post(url, {token: tokenForm}).then(function(response) {
            commentDelete.displayDeleteComment(form)
        }).catch(function(error){
            if (error.response.status === 403) {
                window.alert("Vous n'êtes pas connecté")
            } else {
                window.alert("Erreur, réessayer ultérieurement")
            }
        })

    },

    displayDeleteComment: function (form) {


        let parentDivForm = form.parentNode

        let parentDiv = parentDivForm.parentNode

        parentDiv.parentNode.removeChild(parentDiv)


    }
}

document.addEventListener('DOMContentLoaded', commentDelete.init);