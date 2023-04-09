crud.field("professor_id")
    .onChange(function (field) {
        // eg. show "car_model" if "car_id" is 1
        alert(field.value);
        crud.field("Professor Name").show(field.value == 2);
    })
    .change();
