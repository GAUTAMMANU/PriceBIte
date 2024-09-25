const swiggy = document.getElementById('swiggy');
      const magicpin = document.getElementById('magicpin');

      function checkValue() {
        const value1 = parseInt(document.querySelector(".cart-Value1").innerText.replace("$", ""));
        const value2 = parseInt(document.querySelector(".cart-Value2").innerText.replace("$", ""));
        
        if (value1 < value2) {
          triggerConfetti();
          swiggy.style.backgroundColor = 'green';
        } else {
          triggerConfetti();
          magicpin.style.backgroundColor = 'green';
        }
      }

      function triggerConfetti() {
        const duration = 3 * 1000; // 3 seconds
        confetti({
          particleCount: 100,
          spread: 160,
          origin: { y: 0.6 },
          ticks: 200,
          disableForReducedMotion: true,
          shapes: ['circle', 'square'],
        });

      }

      checkValue();