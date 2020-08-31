<?php 
function ab_cart_shipping_calculate() {
	echo '
		<script>
			jQuery(function ($) {
				$(document).ready(function () {

					if (document.querySelector("#calc_shipping_postcode")) {
						let calcShippingCountry = document.getElementById("calc_shipping_country");
						if (calcShippingCountry) {
							calcShippingCountry.value = "BR";
						}

						let formSubmitButton = document.querySelector(`.shipping-calculator-form button[type="submit"]`);

						function limpa_formulário_cep() {
							// Limpa valores do formulário de cep.
							$("#calc_shipping_city").val("");
							$("#calc_shipping_state").val("");
						}

						//Quando o campo cep perde o foco.
						$("#calc_shipping_postcode").keyup(function () {

							//Nova variável "cep" somente com dígitos.
							var cep = $(this).val().replace(/\D/g, "");

							//Verifica se campo cep possui valor informado.
							if (cep.length == 8) {

								//Expressão regular para validar o CEP.
								var validacep = /^[0-9]{8}$/;

								//Valida o formato do CEP.
								if (validacep.test(cep)) {

									//Preenche os campos com "..." enquanto consulta webservice.
									$("#calc_shipping_city").val("...");
									$("#calc_shipping_state").val("...");

									//Consulta o webservice viacep.com.br/
									$.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

										if (!("erro" in dados)) {
											//Atualiza os campos com os valores da consulta.
											$("#calc_shipping_city").val(dados.localidade);
											$("#calc_shipping_state").val(dados.uf);
											formSubmitButton.removeAttribute("disabled")
										} else {
											//CEP pesquisado não foi encontrado.
											limpa_formulário_cep();
											alert("CEP não encontrado.");
											formSubmitButton.setAttribute("disabled", true)
										}
									});
								} else {
									//cep é inválido.
									limpa_formulário_cep();
									alert("Formato de CEP inválido.");
									formSubmitButton.setAttribute("disabled", true)
								}
							} else {
								//cep sem valor, limpa formulário.
								formSubmitButton.setAttribute("disabled", true)
								limpa_formulário_cep();
							}
						});
					}
				});
			})
		</script>
		<style>
			.woocommerce-shipping-totals.shipping .shipping-calculator-button,
			.shipping-calculator-form #calc_shipping_country_field,
			.shipping-calculator-form #calc_shipping_state_field,
			.shipping-calculator-form #calc_shipping_city_field {
				display: none !important;
			}
			.woocommerce-shipping-totals.shipping .shipping-calculator-form {
				display: block !important;
			}
		</style>
	';
}
add_action('woocommerce_after_cart', 'ab_cart_shipping_calculate');
