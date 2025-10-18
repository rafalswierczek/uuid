#include <windows.h>
#include <bcrypt.h>

void generate_uuid4(char *out) {
    unsigned char uuid[16];
    BCryptGenRandom(NULL, uuid, 16, BCRYPT_USE_SYSTEM_PREFERRED_RNG);
    
    uuid[6] = (uuid[6] & 0x0F) | 0x40;
    uuid[8] = (uuid[8] & 0x3F) | 0x80;
    
    static const char hex[] = "0123456789abcdef";
    
    // Unrolled - brak branching
    out[0] = hex[uuid[0] >> 4];
    out[1] = hex[uuid[0] & 0xF];
    out[2] = hex[uuid[1] >> 4];
    out[3] = hex[uuid[1] & 0xF];
    out[4] = hex[uuid[2] >> 4];
    out[5] = hex[uuid[2] & 0xF];
    out[6] = hex[uuid[3] >> 4];
    out[7] = hex[uuid[3] & 0xF];
    out[8] = '-';
    out[9] = hex[uuid[4] >> 4];
    out[10] = hex[uuid[4] & 0xF];
    out[11] = hex[uuid[5] >> 4];
    out[12] = hex[uuid[5] & 0xF];
    out[13] = '-';
    out[14] = hex[uuid[6] >> 4];
    out[15] = hex[uuid[6] & 0xF];
    out[16] = hex[uuid[7] >> 4];
    out[17] = hex[uuid[7] & 0xF];
    out[18] = '-';
    out[19] = hex[uuid[8] >> 4];
    out[20] = hex[uuid[8] & 0xF];
    out[21] = hex[uuid[9] >> 4];
    out[22] = hex[uuid[9] & 0xF];
    out[23] = '-';
    out[24] = hex[uuid[10] >> 4];
    out[25] = hex[uuid[10] & 0xF];
    out[26] = hex[uuid[11] >> 4];
    out[27] = hex[uuid[11] & 0xF];
    out[28] = hex[uuid[12] >> 4];
    out[29] = hex[uuid[12] & 0xF];
    out[30] = hex[uuid[13] >> 4];
    out[31] = hex[uuid[13] & 0xF];
    out[32] = hex[uuid[14] >> 4];
    out[33] = hex[uuid[14] & 0xF];
    out[34] = hex[uuid[15] >> 4];
    out[35] = hex[uuid[15] & 0xF];
    out[36] = '\0';
}

void generate_uuid4_batch(char *out, int count) {
    static const char hex[] = "0123456789abcdef";
    
    for (int i = 0; i < count; i++) {
        unsigned char uuid[16];
        BCryptGenRandom(NULL, uuid, 16, BCRYPT_USE_SYSTEM_PREFERRED_RNG);
        
        uuid[6] = (uuid[6] & 0x0F) | 0x40;
        uuid[8] = (uuid[8] & 0x3F) | 0x80;
        
        char *p = out + (i * 37);
        
        p[0] = hex[uuid[0] >> 4];
        p[1] = hex[uuid[0] & 0xF];
        p[2] = hex[uuid[1] >> 4];
        p[3] = hex[uuid[1] & 0xF];
        p[4] = hex[uuid[2] >> 4];
        p[5] = hex[uuid[2] & 0xF];
        p[6] = hex[uuid[3] >> 4];
        p[7] = hex[uuid[3] & 0xF];
        p[8] = '-';
        p[9] = hex[uuid[4] >> 4];
        p[10] = hex[uuid[4] & 0xF];
        p[11] = hex[uuid[5] >> 4];
        p[12] = hex[uuid[5] & 0xF];
        p[13] = '-';
        p[14] = hex[uuid[6] >> 4];
        p[15] = hex[uuid[6] & 0xF];
        p[16] = hex[uuid[7] >> 4];
        p[17] = hex[uuid[7] & 0xF];
        p[18] = '-';
        p[19] = hex[uuid[8] >> 4];
        p[20] = hex[uuid[8] & 0xF];
        p[21] = hex[uuid[9] >> 4];
        p[22] = hex[uuid[9] & 0xF];
        p[23] = '-';
        p[24] = hex[uuid[10] >> 4];
        p[25] = hex[uuid[10] & 0xF];
        p[26] = hex[uuid[11] >> 4];
        p[27] = hex[uuid[11] & 0xF];
        p[28] = hex[uuid[12] >> 4];
        p[29] = hex[uuid[12] & 0xF];
        p[30] = hex[uuid[13] >> 4];
        p[31] = hex[uuid[13] & 0xF];
        p[32] = hex[uuid[14] >> 4];
        p[33] = hex[uuid[14] & 0xF];
        p[34] = hex[uuid[15] >> 4];
        p[35] = hex[uuid[15] & 0xF];
        p[36] = '\0';
    }
}
