<template>
    <div id="right">
        <h1>Development CRM</h1>
        <!-- <img src="images/horizontal.png" alt=""> -->

        <div class="horizontal">
            <img src="images/horizontal.png" alt="">
        </div>

        <p>
            Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out
            print, graphic or web designs. The passage is attributed to an unknown typesetter
            in the 15th century who is thought to have scrambled parts of Cicero's De Finibus
            Bonorum et Malorum for use in a type specimen book.
        </p>
        <div class="users-icon"><img src="images/users.png" /></div>
        <div class="tasks">
            <div class="add-tasks">
                <h1>Today's Task</h1>
                <div class="add-action"><img src="images/add.png" /></div>
            </div>

            <ul class="tasks-list">
                <li v-for="task in todayTask" v-bind:key="task.id">
                    <div class="info">
                        <div class="left">
                            <label class="myCheckbox">
                                <input type="checkbox" name="test" :checked="task.completed"
                                    @change="updateTodayTask(task.taskId)" />
                                <span></span>
                            </label>

                            <h4>{{ task.title }}</h4>
                        </div>
                        <div class="right">
                            <img src="images/edit.png" />
                            <img src="images/del.png" @click="deleteTask(task.taskId)" />

                            <button v-bind:class="{
                  inprogress: !task.approved,
                  approved: task.approved,
                }">
                                {{ task.approved ? "Approved" : "In progress" }}
                            </button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="upcoming">
            <div class="add-tasks">
                <h1>Upcoming Task</h1>

                <div class="add-action">
                    <img src="images/add.png" alt="">
                </div>
            </div>

            <form action="" @submit="addUpcomingTask">
                <input type="text" v-model="newTask" />
            </form>

            <ul class="tasks-list">
                <li v-for="upcomingtask in upcomingTask" v-bind:key="upcomingtask.id">
                    <div class="info">
                        <div class="left">
                            <label class="myCheckbox">
                                <input type="checkbox" name="test" :checked="upcomingtask.completed"
                                    @change="checkUpcoming(upcomingtask.taskId)" />
                                <span></span>
                            </label>
                            <h4>{{ upcomingtask.title }}</h4>
                        </div>
                        <div class="right">
                            <img src="images/edit.png" alt="">
                            <img src="images/del.png" alt="" @click="deleteUpcoming(upcomingtask.taskId)">
                            <button v-bind:class="{
                  inprogress: !upcomingtask.approved,
                  approved: upcomingtask.approved,
                }">
                                {{ upcomingtask.approved ? "Approved" : "waiting" }}
                            </button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                todayTask: [],
                upcomingTask: [],
                newTask: "",
            };
        },
        created() {
            this.fetchTodayTasks();
            this.fetchUpcomingTasks();
        },
        methods: {
            //**Upcoming task method *//

            // Get Upcoming task
            fetchUpcomingTasks() {
                fetch("/api/upcoming")
                    .then((res) => res.json())
                    .then(({
                        data
                    }) => {
                        this.upcomingTask = data;
                        // console.log(data);
                    })
                    .catch((err) => console.log(err));
            },

            // Add Upcoming task
            addUpcomingTask(e) {
                e.preventDefault();
                // console.log(this.newTask);

                if (this.upcomingTask.length > 5) {
                    alert("Please complete the upcoming task");
                } else {
                    const newTask = {
                        title: this.newTask,
                        waiting: true,
                        taskId: Math.random().toString(36).substring(7),
                    };

                    //Post Request
                    fetch(`/api/upcoming`, {
                        method: "POST",
                        headers: {
                            "content-type": "application/json",
                        },
                        body: JSON.stringify(newTask),
                    }).then(() => this.upcomingTask.push(newTask));

                    // Clear or Reset new Task
                    this.newTask = "";
                }
            },
            //Delete Upcoming task
            deleteUpcoming(taskId) {
                if (confirm("Are you sure ?")) {
                    fetch(`/api/upcoming/${taskId}`, {
                            method: 'delete',
                        }).then((res) => res.json())
                        .then(() => {
                            this.upcomingTask = this.upcomingTask.filter(
                                ({
                                    taskId: id
                                }) => id !== taskId
                            );
                        }).catch((err) => console.log(err));
                }
            },
            //Check Upcoming task
            checkUpcoming(taskId) {
                if (this.todayTask.length > 4) {
                    alert("Sorry Please complete existing task");
                    window.location.href = "/";
                } else {
                    this.addDailyTask(taskId);
                    console.log("Check");
                    // Delete task from DB
                    fetch(`/api/upcoming/${taskId}`, {
                        method: "delete"
                    }).then(
                        () =>
                        (this.upcomingTask = this.upcomingTask.filter(
                            ({
                                taskId: id
                            }) => id !== taskId
                        ))
                    );
                }
            },

            //**Today Task Method */
            // Get Today task
            fetchTodayTasks() {
                fetch("/api/today")
                    .then((res) => res.json())
                    .then(({
                        data
                    }) => {
                        this.todayTask = data;
                        // console.log(data);
                    })
                    .catch((err) => console.log(err));
            },

            //Add Daily Task
            addDailyTask(taskId) {
                // Get Task
                const task = this.upcomingTask.filter(({
                    taskId: id
                }) => id == taskId)[0];

                // Post Request
                fetch("/api/today", {
                        method: "POST",
                        headers: {
                            "content-type": "application/json",
                        },
                        body: JSON.stringify(task),
                    }).then(() => this.todayTask.unshift(task))
                    .catch((err) => console.log(err));
            },
            //Update today task
            updateTodayTask(taskId){
                if (confirm("Task Completed ? ")) {
                    fetch(`/api/today/${taskId}`, { method:"delete" })
                    .then(() =>{})
                    .then(() => {
                        this.todayTask = this.todayTask.filter(
                            ({ taskId:id }) => id !==taskId
                        );
                    }).catch((err) => console.log(err));
                }
            },
            //Delete Upcoming task
            deleteTask(taskId) {
                if (confirm("Are you sure ?")) {
                    fetch(`/api/today/${taskId}`, {
                            method: 'delete',
                        }).then((res) => res.json())
                        .then(() => {
                            this.todayTask = this.todayTask.filter(
                                ({
                                    taskId: id
                                }) => id !== taskId
                            );
                        }).catch((err) => console.log(err));
                }
            },
        },

    };

</script>

<style>

</style>
