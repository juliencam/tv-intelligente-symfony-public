const like = {

    init: function() {

        //console.log('like');

        let buttonLike = document.querySelector('.js-button-like')
        buttonLike.addEventListener("click", like.handleLike)


    },

    handleLike: function (event) {

        event.preventDefault()

        aElement = event.currentTarget

        divParent = aElement.parentNode

        let url = this.href

        axios.post(url).then(function(response) {

            like.displayLike(response.data.nbrLikes)
            //console.log(response.data);
        }).catch(function(error){
            if (error.response.status === 401) {

                let divTooltiptextTemplate = document.getElementById("div-tooltiptext").content

                let copyDivTooltiptextTemplate = document.importNode(divTooltiptextTemplate, true)
        
                divParent.innerHTML= ""

                divParent.prepend(copyDivTooltiptextTemplate)
    
            } else {
                window.alert("Erreur, réessayer ultérieurement")
            }
        })

    },

    displayLike: function (nbrLikes) {

        let spanNbreLike = document.querySelector(".js-nbr-like")
        spanNbreLike.textContent = nbrLikes

        let likeIcon = document.querySelector(".fa-thumbs-up")

        if (likeIcon.classList.contains('fas')) {
            likeIcon.classList.replace('fas', 'far')
        } else {
            likeIcon.classList.replace('far', 'fas')
        }


    }

}

document.addEventListener('DOMContentLoaded', like.init);