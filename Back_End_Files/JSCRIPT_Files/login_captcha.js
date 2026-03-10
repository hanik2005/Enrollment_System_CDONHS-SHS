function generateCaptcha() {
    var num1 = Math.floor(Math.random() * 10) + 1;
    var num2 = Math.floor(Math.random() * 10) + 1;
    var sum = num1 + num2;

    var num1Box = document.getElementById('captchaNum1');
    var num2Box = document.getElementById('captchaNum2');
    var label = document.getElementById('captchaLabel');
    var answerInput = document.getElementById('captcha');
    var correctSum = document.getElementById('correct_sum');

    if (num1Box) num1Box.textContent = num1;
    if (num2Box) num2Box.textContent = num2;
    if (label) label.textContent = 'Solve Authentication';
    if (correctSum) correctSum.value = sum;
    if (answerInput) answerInput.value = '';
}

document.addEventListener('DOMContentLoaded', function () {
    generateCaptcha();

    var refreshButton = document.getElementById('captchaRefresh');
    if (refreshButton) {
        refreshButton.addEventListener('click', generateCaptcha);
    }
});
