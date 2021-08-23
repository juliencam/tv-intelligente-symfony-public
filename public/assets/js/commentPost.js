const commentPost = { 

    init: function() {

        //console.log('commentPost.js');

        let form = document.querySelector('.js-form-article')
        form.addEventListener("submit", commentPost.handleComment)
    },

    handleComment: function(event) {

        event.preventDefault()

        form = event.currentTarget

        divParent = form.parentNode

        let url = event.currentTarget.getAttribute("action")

        textareaValue = event.currentTarget.querySelector('textarea').value

        event.currentTarget.querySelector('textarea').value = ""

        axios.post(url, {contentComment: textareaValue}).then(function(response) {
            commentPost.displayComment(response.data)
        }).catch(function(error){

            if (error.response.status === 401) {

                let divTooltiptextTemplate = document.getElementById("div-tooltiptext").content

                let copyDivTooltiptextTemplate = document.importNode(divTooltiptextTemplate, true)
        
                divParent.prepend(copyDivTooltiptextTemplate)
                
                form.remove()

            } else {
                window.alert("Erreur, réessayer ultérieurement")
            }
        })

    },

    displayComment: function(responseData ) {

        let divComments = document.querySelector(".js-div-comments")

        let commentContentTemplate = document.getElementById("div-comment-post-template").content

        let copyCommentTemplate = document.importNode(commentContentTemplate, true)

        copyCommentTemplate.querySelector(".js-comment-user-pseudonym").textContent = responseData.pseudonym

        let commentDate = new Date(responseData.comment.createdAt)

        let formatDatePost = ('0'+ commentDate.getDate()).slice(-2)+"/"+('0'+ (commentDate.getMonth()+1)).slice(-2) 
        + "/" + commentDate.getFullYear() + " " + ('0'+ commentDate.getHours()).slice(-2) + ":" 
        + ('0'+ commentDate.getMinutes()).slice(-2)


        copyCommentTemplate.querySelector(".js-comment-date").textContent = "\u00A0" +" publié le : " + formatDatePost


        copyCommentTemplate.querySelector(".js-comment-content").textContent = responseData.comment.content

        let form = copyCommentTemplate.querySelector("form")

        form.action = "/article/deletecomment/" + responseData.comment.id
        //"{{ path('article_deletecomment', {'commentId':" + responseData.commentId + "}) }}"
 
        copyCommentTemplate.querySelector(".token-comment").value = responseData.token


        form.addEventListener("submit", commentPost.deleteComment)

        let buttonEditCommentTemplate = copyCommentTemplate.querySelector('.js-comment-button-edit')

        buttonEditCommentTemplate.href = "/article/" + responseData.postId  + "/editcomment/"+ responseData.comment.id
      

        buttonEditCommentTemplate.addEventListener('click', commentEdit.handleEditComment)

        divComments.prepend(copyCommentTemplate)
        // {{ csrf_token('delete' ~ comment.id) }}

    },

    deleteComment: function (event) {

        event.preventDefault()

        let result = confirm('Êtes-vous sûr de vouloir supprimer ce commentaire?')

        if (result === false) {
            document.location.reload();
            return true
        }

        let tokenForm = event.currentTarget.elements[1].value

        let url = event.currentTarget.getAttribute("action")

        axios.post(url, {token: tokenForm}).then(function(response) {
        }).catch(function(error){
            if (error.response.status === 403) {
                window.alert("Vous n'êtes pas connecté")
            } else {
                window.alert("Erreur, réessayer ultérieurement")
            }
        })

        //event.currentTarget.divComment.remove()
        let parentForm = event.currentTarget.parentNode
        parentForm.parentNode.remove()

    },

    editComment: function (event) {

        event.preventDefault()

    }


}

document.addEventListener('DOMContentLoaded', commentPost.init);