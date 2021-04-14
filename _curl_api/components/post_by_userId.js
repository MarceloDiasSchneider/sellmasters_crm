app.component('get_post_by_userId', {
    template:
        /*html*/
        `<div class="row">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Get posts with userId</h3>
                    </div>
                    <form action="#" @submit.prevent="get_post_by_userId">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="id_post">Set the user ID number</label>
                                <input type="number" class="form-control" id="id_post" min="0" max="10" placeholder="1 to 10" v-model="userId">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Refresh</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8" v-show="userId">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">All posts of userId #{{ userId }}</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- we are adding the accordion ID so Bootstrap's collapse plugin detects it -->
                        <div id="accordion">
                            <div class="card card-light" v-for="post in posts_of_user" :key="post.id">
                                <div class="card-header">
                                    <h4 class="card-title w-100">
                                        <a class="d-block w-100" data-toggle="collapse" :href="'#post' + post.id">
                                            This is the posr #{{ post.id }}
                                        </a>
                                    </h4>
                                </div>
                                <div :id="'post' + post.id" class="collapse" data-parent="#accordion">
                                    <div class="card-body">
                                        <dl>
                                            <dt>{{ post.title }}</dt>
                                            <dd>{{ post.body }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>`,
    data() {
        return {
            userId: null,
            posts_of_user: []
        }
    },
    methods: {
        get_post_by_userId() {
            let myHeaders = new Headers();
            let requestOptions = {
                method: 'GET',
                headers: myHeaders,
                redirect: 'follow'
            };
            let url = "https://jsonplaceholder.typicode.com/posts?userId=" + this.userId

            fetch(url, requestOptions)
                .then(async response => {
                    const data = await response.json();
                    this.posts_of_user = data
                })
                .catch(error => console.log('error', error));
        },
    },
    watch: {
        userId: function () {
            if (this.userId > 0 && this.userId <= 10) {
                this.get_post_by_userId()
            } else {
                this.posts_of_user = {0:{id: 'This user has no posts', title: 'Test', body: 'Successfully'}}
            }
        }
    }

})