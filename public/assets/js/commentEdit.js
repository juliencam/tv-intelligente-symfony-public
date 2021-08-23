const commentEdit = { 

    init: function() {

        //console.log('commentEdit.js');

        let editButtons = document.querySelectorAll('.js-comment-button-edit')

        for (let editButton of editButtons) {

            editButton.addEventListener('click', commentEdit.handleEditComment)
        }

    },

    handleEditComment: function (event) {

        event.preventDefault()

        aElement = event.currentTarget

        parentAElement = aElement.parentNode

        divParent = parentAElement.parentNode

        //console.log(divParent.innerHTML);

        pContentComment = divParent.querySelector(".js-comment-content")

        orginalCommentText = pContentComment.textContent

        let buttonEditDelete = divParent.querySelector('.js-button-edit-delete')

        let orginalContentDivparent = divParent.innerHTML

        divParent.innerHTML = ""


        let url = this.href

        commentEdit.displayForm(url, orginalCommentText, divParent, buttonEditDelete, orginalContentDivparent)





    },

    displayForm: function (url, orginalCommentText, divParent, buttonEditDelete, orginalContentDivparent) {



        let commentContentTemplate = document.getElementById("div-comment-edit-template").content

        let copyCommentContentTemplate = document.importNode(commentContentTemplate, true)

        commentTextareaTeamplate = copyCommentContentTemplate.querySelector("textarea")

        commentTextareaTeamplate.value = orginalCommentText

        commentButtonRegister = copyCommentContentTemplate.querySelector(".js-button-register")
        commentButtonCancel = copyCommentContentTemplate.querySelector(".js-button-cancel")

        commentButtonRegister.url = url
        commentButtonRegister.divParent = divParent
        commentButtonRegister.buttonEditDelete = buttonEditDelete

        commentButtonRegister.addEventListener("click", commentEdit.setComment)

        commentButtonCancel.orginalContentDivparent = orginalContentDivparent
        commentButtonCancel.addEventListener("click", commentEdit.cancelComment)

        divParent.prepend(copyCommentContentTemplate)

    },

    setComment: function(event) {

        event.preventDefault()

        let newCommentText = event.currentTarget.parentNode.querySelector('textarea').value

        let buttonEditDelete = event.currentTarget.buttonEditDelete

        let divParent = event.target.divParent
        
        axios.post(event.target.url, {contentComment: newCommentText}).then(function(response) {

            commentEdit.displayComment(response.data, buttonEditDelete, divParent)

        }).catch(function(error){
            if (error.response.status === 403) {
                window.alert("Vous n'êtes pas connecté")
            } else {
                window.alert("Erreur, réessayer ultérieurement")
            }
        })

        divParent.innerHTML = ""


    },

    displayComment: function (responseData, buttonEditDelete, divParent) {
        

        let commentTemplate = document.getElementById("div-comment-template").content

        let copyCommentTemplate = document.importNode(commentTemplate, true)

        copyCommentTemplate.querySelector(".js-comment-user-pseudonym").textContent = responseData.pseudonym

        let commentDate = new Date(responseData.comment.createdAt)

        let formatDatePost = ('0'+ commentDate.getDate()).slice(-2)+"/"+('0'+ (commentDate.getMonth()+1)).slice(-2) 
        + "/" + commentDate.getFullYear() + " " + ('0'+ commentDate.getHours()).slice(-2) + ":" 
        + ('0'+ commentDate.getMinutes()).slice(-2)

        copyCommentTemplate.querySelector(".js-comment-date").textContent = "\u00A0" +" publié le : " + formatDatePost

        copyCommentTemplate.querySelector(".js-comment-content").textContent = responseData.comment.content

        divParent.prepend(buttonEditDelete)
        divParent.prepend(copyCommentTemplate)


    },

    cancelComment: function (event) {

        event.preventDefault()

        parentDeleteButton = event.currentTarget.parentNode

        divParent = parentDeleteButton.parentNode

        let orginalContentDivparent = event.currentTarget.orginalContentDivparent

        //console.log(orginalContentDivparent);


        divParent.innerHTML = ""
        divParent.innerHTML = orginalContentDivparent


        let editButton = divParent.querySelector('.js-comment-button-edit')

        editButton.addEventListener('click', commentEdit.handleEditComment)

        let formDelete = divParent.querySelector('.js-comment-form-delete')

        formDelete.addEventListener('submit', commentDelete.handleDeleteComment)




    }



}

document.addEventListener('DOMContentLoaded', commentEdit.init);