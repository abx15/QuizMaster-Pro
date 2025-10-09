
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 text-gray-700 mt-16">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row justify-between items-center gap-4">
            
            <!-- Brand -->
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="3" fill="none"/>
                        <path d="M25 28 L29 32 L39 22" stroke="currentColor" stroke-width="3" fill="none"/>
                    </svg>
                </div>
                <span class="text-lg font-bold">QuizMaster Pro</span>
            </div>

            <!--  Copyright  -->
            <div class="text-center md:text-right space-y-0 md:space-y-0">
                <span class="text-sm text-gray-500">
                    &copy; 2025 QuizMaster Pro | Developed by <a  href="https://arun15dev.netlify.app/" class="hover:text-indigo-600 transition-colors cursor:pointer" target="_blank">Arun Kumar Bind</a> | 
                    <a href="mailto:developerarunwork@gmail.com" class="hover:text-indigo-600 transition-colors">developerarunwork@gmail.com</a> | 
                    <a href="https://github.com/abx15" target="_blank" class="hover:text-indigo-600 transition-colors">GitHub</a> | 
                    <a href="https://www.linkedin.com/in/arun-kumar-a3b047353/" target="_blank" class="hover:text-indigo-600 transition-colors">LinkedIn</a>
                </span>
            </div>
        </div>

        <!-- Back to Top Button -->
        <button id="back-to-top" class="fixed bottom-8 right-8 bg-indigo-500 text-white p-3 rounded-full shadow-lg hover:bg-indigo-600 transition-all duration-300 transform hover:scale-110 hidden">
            <i class="fas fa-chevron-up"></i>
        </button>

        <script>
            // Back to Top Button
            const backToTopButton = document.getElementById('back-to-top');
            window.addEventListener('scroll', () => {
                backToTopButton.classList.toggle('hidden', window.pageYOffset < 300);
            });
            backToTopButton.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
        </script>
</body>
</html>
