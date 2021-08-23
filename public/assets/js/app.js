const app = {

    init: function () {

        //console.log("app.js")

        userDelete.init()

        like.init()

        commentPost.init()

        commentDelete.init()

        commentEdit.init()

    },

}

document.addEventListener('DOMContentLoaded', app.init);