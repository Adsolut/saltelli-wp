# GDPR Compliance - {{SITE_NAME}}

## GDPR Requirements

{{SITE_NAME}} processes personal data from EU citizens, requiring full GDPR compliance.

## Implementation Status

### Cookie Consent (✅ Complete)
**Solution**: Iubenda Cookie Law Solution

- **Plugin**: iubenda-cookie-law-solution
- **Languages**: 4 (Italian, English, Spanish, French)
- **Banner**: Active on all pages
- **Prior consent**: Required before tracking cookies

### Privacy Policy (✅ Complete)

**Accessibility**:
- Italian: https://{{SITE_DOMAIN}}/privacy-policy/
- English: https://{{SITE_DOMAIN}}/en/privacy-policy/
- Spanish: https://{{SITE_DOMAIN}}/es/privacy-policy/
- French: https://{{SITE_DOMAIN}}/fr/privacy-policy/

**Content**: Includes:
- Data collected
- Purpose of processing
- Legal basis
- Data retention
- User rights
- Contact information

### Data Subject Rights

**Right to Access**:
```bash
# Export personal data for user
wp user meta get <USER_ID> _export_file_path --allow-root
```

**Right to Erasure**:
```bash
# Delete user and reassign content
wp user delete <USER_ID> --reassign=<ADMIN_ID> --yes --allow-root
```

**Right to Portability**:
- WordPress built-in: Tools → Export Personal Data

### Data Protection

**SSL/HTTPS**: ✅ Active
- All data transmitted encrypted
- Valid SSL certificate

**Database Security**: ✅ Implemented
- Credentials protected in wp-config.php
- Regular backups
- Backup encryption recommended

**Access Control**: ✅ Implemented
- Limited admin accounts
- Strong passwords
- SSH key authentication

## Personal Data Processing

### Data Collected

**WooCommerce Orders**:
- Name
- Email
- Phone (optional)
- Billing address (if required)

**YITH Bookings** (3,649 bookings):
- Customer name
- Customer email
- Booking dates
- Associated product

**WordPress Users**:
- Username
- Email
- Registration date
- Last login

### Legal Basis for Processing

1. **Contract**: Booking/order processing
2. **Consent**: Marketing emails (if opted in)
3. **Legitimate Interest**: Site functionality
4. **Legal Obligation**: Accounting requirements

### Data Retention

**Active Bookings**: Until completed + 1 year
**Completed Orders**: 7 years (accounting law)
**User Accounts**: Until user requests deletion
**Analytics**: Anonymized after 26 months

## Third-Party Services

### Data Processors

1. **Iubenda** - Cookie consent
2. **Stripe** - Payment processing
3. **PayPal** - Payment processing
4. **Email Service** - Transactional emails

**Data Processing Agreements**: Required for all processors

## Compliance Checklist

- [x] Privacy policy published (4 languages)
- [x] Cookie consent banner active
- [x] SSL certificate valid
- [x] Data export capability
- [x] Data erasure capability
- [x] Data breach procedure documented
- [x] Data retention policy defined
- [ ] DPA with all processors (verify)
- [ ] Privacy impact assessment (if high risk)

## User Rights Requests

### Request Process
1. User submits request via contact form or email
2. Verify user identity
3. Process within 30 days
4. Provide data or confirm deletion

### Data Export
```bash
# Generate export for user email
wp user meta update <USER_ID> _wp_privacy_request export --allow-root
```

### Data Deletion
```bash
# Delete user data (reassign posts/orders)
wp user delete <USER_ID> --reassign=1 --yes --allow-root
```

## References
- GDPR implementation (Phase 5): `/docs/PROJECT_PHASES.md`
- Iubenda integration: https://www.iubenda.com/en/help/1235-wordpress-plugin
- Data protection skill: `.claude/skills/security-hardening/data-protection.md`
