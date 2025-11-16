/**
 * Simple notification plugin for Vue 3
 */

export default {
    install(app) {
        // Create notification container
        const createContainer = () => {
            let container = document.getElementById('notification-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'notification-container';
                container.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    pointer-events: none;
                `;
                document.body.appendChild(container);
            }
            return container;
        };

        // Show notification
        const notify = ({ type = 'info', message = '', duration = 3000 }) => {
            const container = createContainer();
            
            const notification = document.createElement('div');
            notification.style.cssText = `
                padding: 12px 20px;
                border-radius: 8px;
                color: white;
                font-size: 14px;
                font-weight: 500;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                pointer-events: auto;
                animation: slideIn 0.3s ease-out;
                min-width: 250px;
                max-width: 400px;
            `;

            // Set background color based on type
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };
            notification.style.backgroundColor = colors[type] || colors.info;

            // Add icon based on type
            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };
            const icon = icons[type] || icons.info;

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 18px;">${icon}</span>
                    <span>${message}</span>
                </div>
            `;

            // Add animation styles
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            `;
            if (!document.getElementById('notification-styles')) {
                style.id = 'notification-styles';
                document.head.appendChild(style);
            }

            container.appendChild(notification);

            // Auto remove after duration
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, duration);
        };

        // Add to Vue instance
        app.config.globalProperties.$notify = notify;
        
        // Also provide as a composable
        app.provide('notify', notify);
    }
};
