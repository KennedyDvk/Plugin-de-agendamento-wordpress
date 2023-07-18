<?php
/*
Plugin Name: Agendamento de Serviço
Description: Um plugin para agendamento de serviços.
Version: 1.0
Author: Kennedy
*/

function agendamento_servico_enqueue_styles()
{
    wp_enqueue_style('agendamento-servico-styles', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-dialog');
}
add_action('wp_enqueue_scripts', 'agendamento_servico_enqueue_styles');

function agendamento_servico_shortcode()
{
    ob_start();
?>
    <style>
        .form-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: aqua;
            border: 5px solid #4bb838;
            border-radius: 5px;
        }

        .form-container h2 {
            text-align: center;
        }

        .form-container label {
            display: block;
            margin-top: 10px;
            margin-bottom: 3px;
        }

        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container select {
            width: 100%;
            margin-top: 5px;
            padding: 10px;
            border: 0px 0px 1px 0px solid #000000;

        }

        .form-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #1f225a;
            color: #d32121;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .form-container input:hover[type="submit"] {
            background-color: #6e72ba;
        }

        .ui-datepicker {
            font-size: 20px;
            font-weight: 600;
            background-color: white;
            width: auto;
            padding: 30px;
            border-radius: 10px;
        }

        .ui-datepicker-inline {
            display: inline-block;
        }

        .ui-datepicker-prev,
        .ui-datepicker-next {
            background-color: #1f225a;
            color: #fff;
            padding: 5px;
        }

        .ui-datepicker-prev:hover,
        .ui-datepicker-next:hover {
            background-color: #6e72ba;
        }
    </style>
    <div class="form-container">
        <h2>Agendamento de Serviço</h2>
        <form id="booking-form">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" required placeholder="Digite seu nome">

            <label for="phone">Telefone:</label>
            <input type="text" id="phone" name="phone" required placeholder="Digite seu telefone">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Digite seu email">

            <label for="service">Tipo de Serviço:</label>
            <select id="service" name="service" required>
                <option value="">Selecione um serviço</option>
                <option value="Serviço 1">Serviço 1</option>
                <option value="Serviço 2">Serviço 2</option>
                <option value="Serviço 3">Serviço 3</option>
            </select>

            <label for="date">Data:</label>
            <input type="text" id="date" name="date" class="datepicker" required placeholder="Selecione uma data">

            <input type="submit" value="Agendar">
        </form>
    </div>

    <script>
        // Lógica para lidar com o envio do formulário
        document.addEventListener("DOMContentLoaded", function() {
            var form = document.getElementById("booking-form");
            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Impede o envio padrão do formulário

                // Lógica para enviar os dados do formulário para o servidor ou realizar outras ações
                // ...

                // Exibir popup de sucesso
                jQuery("#success-dialog").dialog({
                    modal: true,
                    buttons: {
                        OK: function() {
                            jQuery(this).dialog("close");
                        }
                    },
                    close: function() {
                        // Limpar o formulário após o agendamento
                        form.reset();
                    }
                });

                // Enviar email
                var name = document.getElementById("name").value;
                var email = document.getElementById("email").value;
                var service = document.getElementById("service").value;

                var message = "Olá, " + name + "! Seu agendamento para o serviço " + service + " foi efetuado com sucesso.";

                var data = {
                    action: "send_email",
                    name: name,
                    email: email,
                    message: message
                };

                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data);
            });
            // Inicializar o DatePicker
            jQuery(function($) {
                $(".datepicker").datepicker({
                    dateFormat: "dd/mm/yy",
                    minDate: 0,
                    language: "pt-BR",
                    beforeShow: function(input, inst) {
                        inst.dpDiv.css({
                            marginLeft: input.offsetWidth + 50 + 'px',
                            marginTop: -input.offsetHeight + 20 + 'px'
                        });
                    }
                });
            });
        });
    </script>

<?php
    return ob_get_clean();
}
add_shortcode('agendamento_servico', 'agendamento_servico_shortcode');


// Função de envio de email
function send_email()
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $to = $email;
    $subject = 'Agendamento de Serviço - Confirmação';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    $body = "Olá, " . $name . "!<br><br>";
    $body .= "Seu agendamento para o serviço foi efetuado com sucesso.<br><br>";
    $body .= "Detalhes do agendamento:<br>";
    $body .= "- Nome: " . $name . "<br>";
    $body .= "- Email: " . $email . "<br>";
    $body .= "- Mensagem: " . $message . "<br><br>";
    $body .= "Obrigado por agendar com a gente!<br>";

    wp_mail($to, $subject, $body, $headers);

    die(); // Termina a execução após enviar o email
}
add_action('wp_ajax_send_email', 'send_email');
add_action('wp_ajax_nopriv_send_email', 'send_email');
add_action('wp_ajax_success_email', 'success_email');
add_action('wp_ajax_nopriv_success_email', 'success_email');