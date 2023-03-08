<?php

    if(isset($_POST['submit']))
    {
        $mainArray = $_POST['name'];
        for($i =0; $i < count($mainArray); $i++)
        {
            echo "Val : " . $mainArray[$i] . "\n" . $mainArray[$i + 1];
            echo "\n\n";
            $i += 1;
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div id="forms-container">   
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name[]" value="" required>
                <br><br>
                <label>Event:</label>
                <input type="text" name="name[]" value="" required>
                <button type="button" class="remove-form">Remove</button>
                <br><br>
            </div>
        </div>
        
        <button type="button" id="add-form">Add Form</button>
        <button type="submit" name="submit">submit</button>

      </form>
      <script>
        const addFormBtn = document.getElementById('add-form');
        const formsContainer = document.getElementById('forms-container');

        addFormBtn.addEventListener('click', function() 
        {
            const firstForm = document.querySelector('.form-group');
            const clonedForm = firstForm.cloneNode(true);
            formsContainer.appendChild(clonedForm);

            const inputs = clonedForm.querySelectorAll('input');
            inputs.forEach(function(input) {
                input.value = '';
            });

            const removeFormBtns = document.querySelectorAll('.remove-form');
            removeFormBtns.forEach(function(btn) 
            {
                btn.addEventListener('click', function() 
                {
                    const formGroup = btn.closest('.form-group');
                    formGroup.remove();
                });
            });

            const myForm = document.getElementById('.form-group');
            myForm.addEventListener('submit', function(event) 
            {
                event.preventDefault();
            });
        });

      </script>
</body>
</html>