# 💰 PreçoRadar

**Scoutly** é um micro SaaS desenvolvido para monitorar automaticamente preços de concorrentes em e-commerces.  
O sistema coleta periodicamente os valores de produtos cadastrados, armazena o histórico de preços e envia alertas sempre que há variação significativa.

---

## 🚀 Funcionalidades

- 🛒 Cadastro de produtos e URLs de concorrentes  
- 🔁 Monitoramento automático (via agendamento com cronjob)  
- 📊 Histórico de preços com gráficos de variação  
- 🔔 Alertas por e-mail quando o preço muda  
- 👤 Autenticação e planos de assinatura simples  
- ⚙️ API REST construída com Laravel 11  
- 💻 Dashboard moderno em Next.js 15 + Tailwind v4  

---

## 🧱 Stack utilizada

**Frontend:**  
- [Next.js 15](https://nextjs.org/)  
- [Tailwind CSS v4](https://tailwindcss.com/)  
- [Recharts](https://recharts.org/) para gráficos  

**Backend:**  
- [Laravel 11](https://laravel.com/)  
- [Goutte](https://github.com/FriendsOfPHP/Goutte) para scraping  
- [Laravel Scheduler](https://laravel.com/docs/scheduling) para agendamento de tarefas  
- [Mailgun](https://www.mailgun.com/) ou [Resend](https://resend.com/) para alertas por e-mail  
- PostgreSQL para banco de dados  

---
