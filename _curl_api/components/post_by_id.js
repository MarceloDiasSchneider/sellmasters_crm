app.component('get_post_by_id', {
    template:
        /*html*/
        `<div class="row">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Get a post by id</h3>
                    </div>
                    <form action="#" id="get_post" @submit.prevent="get_post_by_id">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="id_post">Set the ID number</label>
                                <input type="number" class="form-control" id="id_post" min="0" max="100" placeholder="1 to 100" v-model="post_id">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Refresh</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8" v-show="post_id">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">This is the posts {{ post_id }}</h3>
                    </div>
                    <div class="card-body">
                        <dl>
                            <dt>{{ post_title }}</dt>
                            <dd>{{ post_body }}</dd>
                        </dl>
                    </div>
                    <div class="card-footer">
                        <p> userId is {{ post_userId }}</p>
                    </div>
                </div>
            </div>
        </div>`,
    data() {
        return {
            post_id: null,
            post_title: null,
            post_body: null,
            post_userId: null
        }
    },
    methods: {
        get_post_by_id() {
            let myHeaders = new Headers();
            let requestOptions = {
                method: 'GET',
                headers: myHeaders,
                redirect: 'follow'
            };
            let url = "https://jsonplaceholder.typicode.com/posts/" + this.post_id

            fetch(url, requestOptions)
                .then(async response => {
                    const data = await response.json();
                    this.post_title = data.title
                    this.post_body = data.body
                    this.post_userId = data.userId
                })
                .catch(error => console.log('error', error));
        },
    },
    watch: {
        post_id: function () {
            if (this.post_id > 0 && this.post_id <= 100) {
                this.get_post_by_id()
            } else {
                this.post_title = null
                this.post_body = null
                this.post_userId = null
            }
        }
    }

})