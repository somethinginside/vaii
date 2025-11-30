const todoList = [];
const baseTodoId = 'todoitem';

function deleteElement(id) {
    const index = todoList.findIndex(item => item.id === id);
    todoList.splice(index, 1);
    document.getElementById(baseTodoId + id).remove();
}

function addToDo() {
    const form = document.forms.toDoForm;
    const newTodo = {
        id: createNewId(), 
        title: form.elements.title.value,
        description: form.elements.description.value,
        date: form.elements.date.value
    }
    todoList.push(newTodo);
    addToDoToHtml(newTodo);
}

function createNewId() {
    return todoList.length === 0 ?
        1 : Math.max(
            ...todoList.map(todo => todo.id)
        ) + 1;
}

function addToDoToHtml(newToDo) {
    const div = document.createElement('div');
    div.id = baseTodoId + newToDo.id;
    div.className = 'row my-3';
    div.innerHTML = `<div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"> ${newToDo.title} </h5>
                                <p class="card-text"> ${newToDo.description} </p>
                                <a> ${newToDo.date} </a>
                            </div>
                        </div>
                     </div>`
    document.getElementById('toDoContainer').append(div);
    localStorage.setItem("todoList", JSON.stringify(todoList));
}
