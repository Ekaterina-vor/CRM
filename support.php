fa-arrow-left"></i> Вернуться к клиентам</a>
         
         <div class="support-container">
             <h1 class="support-title">Техническая поддержка</h1>
             
             <form class="support-form" action="#" method="post">
                 <div class="form-group">
                     <label for="subject">Тема обращения</label>
                     <input type="text" id="subject" name="subject" required>
                 </div>
                 
                 <div class="form-group">
                     <label for="priority">Приоритет</label>
                     <select id="priority" name="priority">
                         <option value="low">Низкий</option>
                         <option value="medium" selected>Средний</option>
                         <option value="high">Высокий</option>
                     </select>
                 </div>
                 
                 <div class="form-group">
                     <label for="message">Описание проблемы</label>
                     <textarea id="message" name="message" required></textarea>
                 </div>
                 
                 <div class="form-actions">
                     <button type="submit" class="btn btn-primary">Отправить запрос</button>
                     <button type="button" class="btn btn-secondary" onclick="window.location.href='clients.php'">Отмена</button>
                 </div>
             </form>
         </div>
     </div>
 </body>
 </html>