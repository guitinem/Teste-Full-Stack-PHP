const formCarFields = ['veiculo', 'marca', 'ano', 'descricao'];

const createCardCarHtml = ({ id, veiculo, marca, ano, vendido, descricao }) => {
    let html = `<div id="card-car-${id}" class="card-car">`;

    // Title
    html += `
        <div class="card-car-title">
            <span id="card-car-veiculo-${id}">${veiculo}</span>
            <hr class="line-card">
        </div>
    `;

    // Content
    html += `
        <div class="card-car-content">
            <div class="card-car-content-group">
                <div class="card-car-content-info">
                    <p>Marca</p>
                    <span id="card-car-marca-${id}">${marca}</span>
                </div>
                <div class="card-car-content-info">
                    <p>Ano</p>
                    <span id="card-car-ano-${id}">${ano}</span>
                </div>
            </div>
            <div class="card-car-content-description">
                <p>Descrição</p>
                <textarea readonly cols="29" rows="9" id="card-car-descricao-${id}">${descricao}</textarea>
            </div>
    `;

    // Sold or not
    html += `
            <div class="card-car-content-seller">
                <p>Vendido</p>
                ${vendido === '1' ? 
                    `<img src="assets/check-circle.svg" alt="vendido">` : 
                    `<img src="assets/x-circle.svg" alt="à venda">`}
            </div>
        </div>
        `

    // Button
    html += `
        <div class="card-car-buttons">
            <button class="deleteCar" type="button" onclick="deleteCar(${id})">
                <img src="assets/trash-2.svg" alt="deletar">
            </button>
            <button class="editCar" type="button" onclick="openEditCarModal(${id})">
                <img src="assets/edit.svg" alt="editar">
            </button>
        </div>
    </div>`;

    return html
}


/**
 * Create a card contain the car info
 * 
 * @param {array} data 
 */
function createCardCarInfo(data) {
    document.getElementById('container').innerHTML = '';

    let html_cars = "";


    if (data.length === 0) {
        html_cars = `
            <div class="container-no_content">
                <strong>Sem veículos registrados</strong>
                <p>adicione algum veículo clicando no botão acima</p>
            </div>
        `;
    } else {
        data.map(car => {
            const { id, ano, marca, veiculo, vendido, descricao } = car;
            const html = createCardCarHtml({
                id,
                ano,
                marca,
                veiculo,
                vendido,
                descricao
            })

            html_cars += html;
        });
    }

    document.getElementById('container').innerHTML = html_cars;
}


/**
 * Open modal with car information to edit
 * 
 * @param {string} id  
 */
function openEditCarModal(id) {

    formCar.setAttribute('car_id', id);

    document.getElementById('modal-car-title').textContent = "Detalhes do veículo";

    fetch(api + `/${id}`).then(response => 
        response.json().then(result => {

            const car = result[0];
            
            formCarFields.map(field => {
                formCar.elements[field].value = car[field];
            });

            if (car.vendido === '1') {
                formCar.elements['vendido'].checked = true;
            }

            modalCar.style.display = "block";
    }))
}

/**
 * Handle the submit to create/edit a car
 * 
 * @param {Event} event
 * @param {HTMLElement} form
 */
function handleSubmitCarForm(event, form) {
    const data = {};
    let failedFields = [];
    event.preventDefault();

    // Validate fields
    formCarFields.map(field => {
        if (form.elements[field].value === "") {
            failedFields.push(field);
        }

        data[field] = form.elements[field].value;
    });

    data['vendido'] = document.getElementById('vendido').checked ? '1' : '0';

    if (failedFields.length !== 0) {
        alert(`Por favor, verifique os seguintes campos:
                ${failedFields.map(field => `- ${field}\n`)}
        `)
        return
    }

    // Check if post or put
    const car_id = form.getAttribute('car_id');

    if (car_id !== "") {
        editCar(data, car_id);
    } else {
        createCar(data);
    }
}

/**
 * Handle the submit to search a car
 * 
 * @param {Event} event 
 * @param {HTMLElement} form 
 */
function handleSubmitSearchForm(event, form) {
    event.preventDefault();

    let queryString = "";
    const formSearchFields = formCarFields.filter(field => field !== 'descricao');
    
    formSearchFields.map(field => {
        const value = form.elements[field].value;
        if (value !== "") {
            queryString += `${field}=${value}&`;
        }
    });

    if (form.elements['vendido_true'].checked) {
        queryString += 'vendido=1';
    }

    if (form.elements['vendido_false'].checked) {
        queryString += 'vendido=0';
    }
      

    fetch(api + `?${queryString}`).then(response => {
        response.json().then(result => {
            createCardCarInfo(result);
            modalSearch.style.display = "none";
        })
    });

}

/**
 * Create car
 * 
 * @param {Object} body 
 */
function createCar(body) {
    fetch(api, {
        method: 'post',
        body: JSON.stringify(body)
    }).then(response => {
        response.json().then(result => {
            const { id, ano, marca, veiculo, vendido, descricao } = result;
            const htmlNewCar = createCardCarHtml({
                ano,
                id,
                marca,
                veiculo,
                vendido,
                descricao
            });

            let form = document.getElementById('container');

            form.innerHTML = form.innerHTML + htmlNewCar;

            alert('Veículo cadastrado');

            modalCar.style.display = "none";
            formCar.reset();
        })
    });   
}


/**
 * Edit car
 * 
 * @param {Object} body
 * @param {string} id
 */
function editCar(body, id) {
    fetch(api + `/${id}`, {
        method: 'PUT',
        body: JSON.stringify(body),
        mode: "same-origin"
    }).then(response => {
        response.json().then(result => {
        
            formCarFields.map(field => {
                document.getElementById(`card-car-${field}-${id}`).textContent = result[field];
            });

            modalCar.style.display = "none";
            alert('Veículo alterado com sucesso');
            formCar.reset();
        })
    });   
}


/**
 * Delete the selected car
 */
function deleteCar(id) {
   if (!confirm('Deseja deletar o registro deste veículo?')) {
       return;
   }

   fetch(api + `/${id}`, {
       method: 'DELETE',
       mode: "same-origin"
   }).then(() => {
       document.getElementById(`card-car-${id}`).remove();
       alert('Registro de veículo removido com sucesso!')
   });
}
