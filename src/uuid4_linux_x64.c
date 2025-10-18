#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>

static int urandom_fd = -1;

static void init_random() {
    if (urandom_fd < 0) {
        urandom_fd = open("/dev/urandom", O_RDONLY);
    }
}

static void read_random(unsigned char *buf, size_t size) {
    ssize_t result = read(urandom_fd, buf, size);

    if (result != (ssize_t)size) {
        abort();
    }
}

void generate_uuid4(char *out) {
    init_random();
    
    unsigned char uuid[16];
    read_random(uuid, 16);
    
    uuid[6] = (uuid[6] & 0x0F) | 0x40;
    uuid[8] = (uuid[8] & 0x3F) | 0x80;
    
    static const char hex[] = "0123456789abcdef";
    
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
    init_random();
    
    static const char hex[] = "0123456789abcdef";
    
    for (int n = 0; n < count; n++) {
        unsigned char uuid[16];
        read_random(uuid, 16);
        
        uuid[6] = (uuid[6] & 0x0F) | 0x40;
        uuid[8] = (uuid[8] & 0x3F) | 0x80;
        
        char *p = out + (n * 37);
        
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
