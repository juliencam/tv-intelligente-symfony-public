const contact = {

    init: function() {

        //console.log('like');

        if (document.querySelector('.js-a-contact')) {
            let buttonContact = document.querySelector('.js-a-contact')
            buttonContact.addEventListener("click", contact.handleA)
        }
    },

    handleA: function (event) {
        event.preventDefault()

        aElement = event.currentTarget

        divParent = aElement.parentNode

        let divTooltiptextTemplate = document.getElementById("div-tooltiptext").content

        let copyDivTooltiptextTemplate = document.importNode(divTooltiptextTemplate, true)

        divParent.prepend(copyDivTooltiptextTemplate)

        aElement.remove()

    }
}

document.addEventListener('DOMContentLoaded', contact.init);