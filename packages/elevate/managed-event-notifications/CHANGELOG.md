# Changelog

All notable changes to the Managed Event Notifications package will be documented in this file.

## [1.0.0] - 2024-11-14

### Added

#### Core Features
- Initial release of managed event notifications system
- NotificationManager for centralized notification handling
- Support for customer and staff notifications
- Multi-channel support (email, database)
- Queue support for asynchronous sending

#### Resend Integration
- Custom ResendTransport for Laravel Mail
- Full Resend API integration
- Support for attachments, CC, BCC, Reply-To
- Environment-based configuration

#### Customer Notifications (12 types)
- Order confirmation
- Order invoice
- Order edited
- Order canceled
- Draft order invoice
- Shipping confirmation
- Ready for local pickup
- Pickup confirmed
- Out for delivery
- Locally delivered
- Missed local delivery
- New gift card
- Gift card receipt
- Store credit issued

#### Staff Notifications (8 types)
- New order
- New return request
- New draft order
- New subscription order
- Canceled subscription
- Payment failure
- Inventory failure
- Sales attribution edited

#### Templates
- 20+ pre-built Blade email templates
- File-based template system for easy customization
- Mobile-responsive layouts
- Dynamic data support
- Publishable templates

#### Configuration
- Comprehensive configuration file
- Per-notification enable/disable
- Customizable subject lines
- Channel selection per notification
- Staff recipient groups
- Queue configuration

#### Console Commands
- `notifications:list` - List all available notifications
- `notifications:test` - Send test notifications

#### Documentation
- README with feature overview
- Integration guide with step-by-step setup
- Usage examples with code samples
- Package summary
- Environment configuration example

#### Database
- Migration for notifications table
- Support for database notification channel

#### Developer Tools
- Facade for easy access (ManagedNotifications)
- Service provider with auto-discovery
- Publishable config and templates
- Type-safe notification handling

### Technical Details
- Laravel 10.x, 11.x, 12.x compatibility
- PHP 8.1+ requirement
- PSR-4 autoloading
- Follows Laravel package conventions
- Queue-aware notifications
- Extensible architecture

---

## Future Roadmap

### Planned for v1.1.0
- [ ] SMS notifications via Twilio
- [ ] Slack notifications
- [ ] Webhook notifications
- [ ] Notification preferences UI

### Planned for v1.2.0
- [ ] Multi-language support
- [ ] Template builder UI
- [ ] A/B testing support
- [ ] Analytics dashboard

### Planned for v2.0.0
- [ ] React Email templates
- [ ] Push notifications
- [ ] Advanced scheduling
- [ ] Notification workflows
